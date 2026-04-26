<?php

namespace App\Http\Controllers;

use App\Models\League;
use App\Models\LeagueJoinRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LeagueController extends Controller
{
    private const MAX_PRIVATE_LEAGUES = 3;

    public function index(Request $request)
    {
        $user = $request->user();
        $query = trim((string) $request->query('q', ''));
        $searchResults = collect();

        $user->load([
            'leagues.owner',
            'ownedLeague.members',
            'ownedLeague.pendingJoinRequests.user',
            'leagueJoinRequests.league',
        ]);

        if ($query !== '') {
            $searchResults = League::with('owner')
                ->withCount('members')
                ->when(is_numeric($query), function ($builder) use ($query) {
                    $builder->where('id', (int) $query)
                        ->orWhere('name', 'like', '%' . $query . '%');
                }, function ($builder) use ($query) {
                    $builder->where('name', 'like', '%' . $query . '%');
                })
                ->orderBy('name')
                ->limit(10)
                ->get();
        }

        return view('leagues.index', [
            'leagues' => $user->leagues,
            'ownedLeague' => $user->ownedLeague,
            'pendingRequests' => $user->leagueJoinRequests
                ->where('status', 'pending'),
            'memberLeagueIds' => $user->leagues->pluck('id'),
            'requestStatuses' => $user->leagueJoinRequests->pluck('status', 'league_id'),
            'searchQuery' => $query,
            'searchResults' => $searchResults,
            'maxPrivateLeagues' => self::MAX_PRIVATE_LEAGUES,
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => 'required|string|max:80|unique:leagues,name',
        ]);

        if ($user->ownedLeague()->exists()) {
            throw ValidationException::withMessages([
                'name' => 'You can only own one league.',
            ]);
        }

        if ($user->leagues()->count() >= self::MAX_PRIVATE_LEAGUES) {
            throw ValidationException::withMessages([
                'name' => 'You can join up to three private leagues.',
            ]);
        }

        DB::transaction(function () use ($user, $request) {
            $league = League::create([
                'name' => $request->name,
                'owner_id' => $user->id,
            ]);

            $league->members()->attach($user->id);
        });

        return redirect()
            ->route('leagues.index')
            ->with('success', 'League created successfully.');
    }

    public function requestJoin(Request $request, League $league)
    {
        $user = $request->user();

        if ($league->members()->whereKey($user->id)->exists()) {
            throw ValidationException::withMessages([
                'league' => 'You are already a member of this league.',
            ]);
        }

        if ($user->leagues()->count() >= self::MAX_PRIVATE_LEAGUES) {
            throw ValidationException::withMessages([
                'league' => 'You can join up to three private leagues.',
            ]);
        }

        LeagueJoinRequest::updateOrCreate(
            [
                'league_id' => $league->id,
                'user_id' => $user->id,
            ],
            [
                'status' => 'pending',
            ]
        );

        return redirect()
            ->route('leagues.index', ['q' => $league->id])
            ->with('success', 'Join request sent.');
    }

    public function acceptRequest(Request $request, League $league, LeagueJoinRequest $joinRequest)
    {
        $this->ensureOwner($request->user(), $league);
        $this->ensureRequestBelongsToLeague($joinRequest, $league);

        DB::transaction(function () use ($league, $joinRequest) {
            $member = User::lockForUpdate()->findOrFail($joinRequest->user_id);

            if ($member->leagues()->count() >= self::MAX_PRIVATE_LEAGUES) {
                throw ValidationException::withMessages([
                    'request' => 'This user already belongs to three private leagues.',
                ]);
            }

            $league->members()->syncWithoutDetaching([$member->id]);
            $joinRequest->update(['status' => 'accepted']);
        });

        return redirect()
            ->route('leagues.index')
            ->with('success', 'Member accepted.');
    }

    public function rejectRequest(Request $request, League $league, LeagueJoinRequest $joinRequest)
    {
        $this->ensureOwner($request->user(), $league);
        $this->ensureRequestBelongsToLeague($joinRequest, $league);

        $joinRequest->update(['status' => 'rejected']);

        return redirect()
            ->route('leagues.index')
            ->with('success', 'Request rejected.');
    }

    public function removeMember(Request $request, League $league, User $user)
    {
        $this->ensureOwner($request->user(), $league);

        if ($league->owner_id === $user->id) {
            throw ValidationException::withMessages([
                'member' => 'The league owner cannot be removed.',
            ]);
        }

        $league->members()->detach($user->id);

        return redirect()
            ->route('leagues.index')
            ->with('success', 'Member removed.');
    }

    private function ensureOwner(User $user, League $league): void
    {
        abort_unless($league->owner_id === $user->id, 403);
    }

    private function ensureRequestBelongsToLeague(LeagueJoinRequest $joinRequest, League $league): void
    {
        abort_unless($joinRequest->league_id === $league->id, 404);
    }
}

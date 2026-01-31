@if($events->count())
    <div class="space-y-4">
        @foreach($events as $event)
            <div style="margin-bottom:15px;" class="bg-white rounded-xl shadow p-4">

                <div class="flex justify-between items-start">
                    <div>
                        {{-- Título --}}
                        <h3 class="font-bold text-lg">
                            {{ $event->title }}
                        </h3>

                        {{-- Teams --}}
                        <p class="text-sm text-gray-600">
                            {{ optional($event->teamA)->name ?? '—' }}
                            vs
                            {{ optional($event->teamB)->name ?? '—' }}
                        </p>

                        {{-- Fecha --}}
                        <p class="text-xs text-gray-400 mt-1">
                            Starts at:
                            {{ $event->starts_at->timezone('Europe/Madrid')->format('d M Y · H:i') }}
                        </p>
                    </div>

                    {{-- Actions --}}
                    <div class="flex gap-2">
                        {{-- Draft → Open --}}
                        @if($mode === 'draft')
                            <form method="POST" action="{{ route('admin.events.open', $event) }}">
                                @csrf
                                <button style="color:black;" class="px-3 py-1 bg-green-600 text-white rounded font-bold">
                                    Open betting
                                </button>
                            </form>
                        @endif

                        {{-- Open → Settle --}}
                        @if($mode === 'open')
                            <form method="POST"
                                  action="{{ route('admin.events.settle', $event) }}"
                                  class="flex gap-2">
                                @csrf

                                <select name="result"
                                        required
                                        class="border rounded px-2 py-1 text-sm">
                                    <option value="">Result</option>
                                    <option value="team_a">
                                        {{ optional($event->teamA)->name ?? 'Team A' }}
                                    </option>
                                    <option value="team_b">
                                        {{ optional($event->teamB)->name ?? 'Team B' }}
                                    </option>
                                    <option value="draw">Draw</option>
                                </select>

                                <button class="px-3 py-1 bg-red-600 text-white rounded font-bold">
                                    Settle
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                {{-- Finished --}}
                @if($mode === 'finished')
                    <div class="mt-3 text-sm font-bold text-green-600">
                        Result:
                        @if($event->result === 'team_a')
                            {{ optional($event->teamA)->name ?? '—' }}
                        @elseif($event->result === 'team_b')
                            {{ optional($event->teamB)->name ?? '—' }}
                        @else
                            Draw
                        @endif
                    </div>
                @endif

            </div>
        @endforeach
    </div>
@else
    <p class="text-gray-500 italic">
        No events in this section.
    </p>
@endif

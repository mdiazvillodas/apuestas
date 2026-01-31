<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CoinGrant;

class CoinGrantController extends Controller
{
    public function index()
    {
        $grants = CoinGrant::with(['user', 'admin'])
            ->latest()
            ->get();

        return view('admin.coin-grants.index', compact('grants'));
    }
}

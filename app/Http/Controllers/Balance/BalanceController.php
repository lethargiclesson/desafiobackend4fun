<?php

namespace App\Http\Controllers\Balance;

use App\Models\Balance;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BalanceController extends Controller
{
    public function addBalance(Request $request)
    {
        Balance::where('user_id', $request->user_id)->update(['balance' => $request->balance]);
    }
}

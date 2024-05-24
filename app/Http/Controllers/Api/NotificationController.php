<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function show (Request $request) {
        $notification  = Notification::where('user_id', Auth::id())
                                    ->paginate($request->query('number') ?? 15);
        return response()->json(['status' => true, 'data' => $notification], 200);  
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function show (Request $request) {
        $notification  = Notification::where('user_id', Auth::id())
                                    ->orderByDesc('created_at')
                                    ->paginate($request->query('number') ?? 15);
                                    
        return NotificationResource::collection($notification);
    }
}

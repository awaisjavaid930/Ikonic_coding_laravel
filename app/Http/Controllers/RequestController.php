<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\RequestResource;
use App\Http\Resources\UserResource;
use App\Models\Request as ConnectionRequst;
use Auth;
use App\Models\User;

class RequestController extends Controller
{
    public function suggestedRequest()
    {
        $senderId = ConnectionRequst::pluck('sender_id')->toArray();
        $receiverId = ConnectionRequst::pluck('receiver_id')->toArray();
        $userIds = array_unique(array_merge($senderId, $receiverId));
        $users = User::whereNotIn('id',$userIds)->get();
        return response()->json(['status' => 200 , 'data' => UserResource::collection($users) ]);                
    
    }
    
    public function pendingRequest()
    {
        $pendingRequests = ConnectionRequst::whereStatus('pending')
                            ->whereReceiverId(Auth::id())->get();
        return response()->json(['status' => 200 , 'data' => RequestResource::collection($pendingRequests) ]);
    }
    
    public function approvedRequest(ConnectionRequst $request)
    {
        $request->update(['status' => 'approved']);
        return response()->json(['status' => 201 , 'message' => 'request approved!' ]);
    }
    
    public function withdrawRequest(ConnectionRequst $request)
    {
        $request->delete();
        return response()->json(['status' => 201 , 'message' => 'request withdraw!' ]);
    }
    
    public function getApprovedRequest()
    {
        $approvedRequests = ConnectionRequst::whereStatus('approved')
                            ->whereReceiverId(Auth::id())->get();
        return response()->json(['status' => 200 , 'data' => RequestResource::collection($approvedRequests) ]);                
    }

    
    
}

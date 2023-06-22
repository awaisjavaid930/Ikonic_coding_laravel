<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\RequestResource;
use App\Http\Resources\UserResource;
use App\Models\Request as ConnectionRequst;
use Auth;
use App\Models\User;
use DB;

class RequestController extends Controller
{
    // suggested
    public function suggestedRequest()
    {
        $senderId = ConnectionRequst::pluck('sender_id')->toArray();
        $receiverId = ConnectionRequst::pluck('receiver_id')->toArray();
        $userIds = array_unique(array_merge($senderId, $receiverId));
        $users = User::whereNotIn('id',$userIds)->paginate(3);
        return response()->json(['status' => 200 , 'data' => $users]);
    }
    
    // get pending send request
    public function sendRequest()
    {
        $pendingRequests = ConnectionRequst::whereStatus('pending')
                            ->whereSenderId(Auth::id())->with('receiver')->get();
        return response()->json(['status' => 200 , 'data' => RequestResource::collection($pendingRequests) ]);
    }

    // get pending received request
    public function receivedRequest()
    {
        $pendingRequests = ConnectionRequst::whereStatus('pending')
                            ->whereReceiverId(Auth::id())->with('sender')->get();
        return response()->json(['status' => 200 , 'data' => $pendingRequests]);
    }
    
    // post send request
    public function createRequest(Request $request)
    {
        $request['sender_id'] = Auth::id();
        $request['receiver_id'] = $request['receiver_id'];
        $request['status'] = 'pending';
        ConnectionRequst::create($request->all());    
        return response()->json(['status' => 201 , 'message' => 'request saved!']);    
    }

    // approved request
    public function approvedRequest(Request $request)
    {
        ConnectionRequst::findOrFail($request['request_id'])->update(['status' => 'approved']);
        return response()->json(['status' => 201 , 'message' => 'request approved!' ]);
    }
    
    // withdraw or delete request 
    public function withdrawRequest(Request $request)
    {
        ConnectionRequst::findOrFail($request['request_id'])->delete();
        return response()->json(['status' => 201 , 'message' => 'request withdraw!']);
    }
    
    public function getApprovedRequest()
    {
        $approvedRequests = ConnectionRequst::whereStatus('approved')
                            // ->whereReceiverId(Auth::id())
                            ->with('sender')->get();
        return response()->json(['status' => 200 , 'data' => RequestResource::collection($approvedRequests) ]);                
    }
    
    public function getMutualRequest()
    {
        $mutualConnections = User::select('users.*','requests.*',DB::raw('count(requests.id) as mutual_connection'))
            ->join('requests','users.id','requests.sender_id')
            ->where('requests.status','approved')
            ->groupBy('requests.receiver_id')
            ->get();

        return response()->json(['status' => 200 , 'data' => $mutualConnections ]);                
        
    }
    
    
}

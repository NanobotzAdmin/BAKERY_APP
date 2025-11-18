<?php

namespace App\Http\Controllers;

use App\sessionActivity;
use Carbon\Carbon;

class UserActivityManagementController extends Controller
{

    public function saveActivity($activityType, $description)
    {
        // session Id
        $sessionId = session('session_id');

        //Save user activity
        $userActivity = new sessionActivity();
        $userActivity->sm_session_id = $sessionId;
        $userActivity->activity_type = $activityType;
        $userActivity->created_at = Carbon::now();
        $userActivity->description = $description;

        $userActivity->save();
    }

}

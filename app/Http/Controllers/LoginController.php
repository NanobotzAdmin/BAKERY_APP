<?php

namespace App\Http\Controllers;

use App\User;
use App\UserRole;
use App\SmSession;
use App\UserLogin;
use Carbon\Carbon;
use App\UserProfile;
use App\STATIC_DATA_MODEL;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    public function adminLogin(Request $request)
    {
        // login validation
        $this->validate($request, [
            'username' => 'required',
            'password' => 'required',
        ]);

        // check user
        $login = UserLogin::where('user_name', request('username'))
            ->where('password', request('password'))
            ->first();

        if ($login) {
            $user = User::where('um_user_login_id', $login->id)->first();
            // $userProfile = UserProfile::where('um_user_id', $user->id)->first();
            if ($user->is_active == STATIC_DATA_MODEL::$Active) {
                // if success
                if (SmSession::where([['um_user_login_id', $login->id], ['is_active', STATIC_DATA_MODEL::$Active]])->exists()) {
                    // deactivate past session
                    $deactivateSesiion = SmSession::where('um_user_login_id', $login->id)->update(['is_active' => STATIC_DATA_MODEL::$Inactive]);
                    // save session
                    SmSession::create([
                        'um_user_login_id' => $login->id,
                        'ip_address' => $request->ip(),
                        'time_in' => Carbon::now(),
                        'time_out' => NULL,
                        'is_active' => STATIC_DATA_MODEL::$Active,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ]);

                    $sessionId = DB::table('sm_session')->latest()->first();

                    session([
                        'logged_user_id' => $user->id,
                        'session_id' => $sessionId->id,
                        'user_type' => $user->pm_user_role_id,
                    ]);

                    //Save user activity
                    $userActivity = new UserActivityManagementController();
                    $userActivity->saveActivity(STATIC_DATA_MODEL::$logIn, "log User ,User Id - " .  $user->id);

                    return redirect('/admindashboard');

                } else {

                    //save session
                    SmSession::create([
                        'um_user_login_id' => $login->id,
                        'ip_address' => $request->ip(),
                        'time_in' => Carbon::now(),
                        'time_out' => NULL,
                        'is_active' => STATIC_DATA_MODEL::$Active,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ]);

                    $sessionId = DB::table('sm_session')->latest()->first();

                    session([
                        'logged_user_id' => $user->id,
                        'session_id' => $sessionId->id,
                        'user_type' => $user->pm_user_role_id,
                    ]);

                    //Save user activity
                    $userActivity = new UserActivityManagementController();
                    $userActivity->saveActivity(STATIC_DATA_MODEL::$logIn, "log User ,User Id - " .  $user->id);

                    return redirect('/admindashboard');
                }

            } else {
                session()->flash('message', 'Your account is not activate. please contact admin');
                session()->flash('flash_message_type', 'alert-danger');
                return redirect('/admin_login');
            }
        } else {
            // if error
            session()->flash('message', 'Username or Password is incorrect');
            session()->flash('flash_message_type', 'alert-danger');

            return redirect('/admin_login');
        }
    }


    public function logout(Request $request)
    {
        // logged user id
        $logged_user = session('logged_user_id');
        $sessionId = session('session_id');

        $userActivity = new UserActivityManagementController();
        $userActivity->saveActivity(STATIC_DATA_MODEL::$update, "Log Out User , UserID - " . $logged_user);

        $SmSession = SmSession::find($sessionId);
        $SmSession->time_out = Carbon::now();
        $SmSession->is_active = STATIC_DATA_MODEL::$Inactive;
        $SmSession->save();

        Session::flush();
        return redirect('/');
    }

}

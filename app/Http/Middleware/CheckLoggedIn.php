<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr; // Import the Arr class

class CheckLoggedIn
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param  string $role
     * @return mixed
     */
    public function handle($request, Closure $next, $role)
    {
        // Check for admin dashboard access
        if ($role === 'adminDashboard') {
            if (session('logged_user_id') === null) {
                return redirect('/admin_login');
            } else {
                return $next($request);
            }
        } else {
            // Check for logged-in user
            if (session('logged_user_id') === null) {
                return redirect('/admin_login');
            } else {
                // Get user privileges
                $privilages = DB::table('um_user_has_interface_components')
                    ->select('pm_interfaces.path')
                    ->distinct()
                    ->join('pm_interface_components', 'um_user_has_interface_components.pm_interface_components_id', '=', 'pm_interface_components.id')
                    ->join('pm_interfaces', 'pm_interface_components.pm_interfaces_id', '=', 'pm_interfaces.id')
                    ->join('pm_interface_topic', 'pm_interfaces.pm_interface_topic_id', '=', 'pm_interface_topic.id')
                    ->where('um_user_has_interface_components.um_user_id', session('logged_user_id'))
                    ->get();

                // Initialize the array for paths
                $arrayBlockExtent = [];

                // Prepend each path to the array
                foreach ($privilages as $privilage) {
                    $arrayBlockExtent = Arr::prepend($arrayBlockExtent, $privilage->path);
                }

                // Check if the role is in the array of allowed paths
                if (in_array($role, $arrayBlockExtent)) {
                    return $next($request);
                } else {
                    return redirect(route('adminDashboard'));
                }
            }
        }
    }
}

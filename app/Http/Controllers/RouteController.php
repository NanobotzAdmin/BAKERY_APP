<?php

namespace App\Http\Controllers;

use App\STATIC_DATA_MODEL;
use App\Customer;
use App\Routes;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RouteController extends Controller
{
    public function adminCreateRouteIndex()
    {
        $routes = Routes::all();
        $ActiveRoutes = Routes::where('is_active', STATIC_DATA_MODEL::$Active)->get();
        return view('vehicle.arrangeVehicle.route.createroute', compact('routes', 'ActiveRoutes'));
    }

    public function saveRoute(Request $request)
    {

        $this->validate($request, [
            'routeName' => 'required',

        ]);

        if (Routes::where('route_name', request('routeName'))->exists()) {
            session()->flash('message', 'Route Name already exits!!');
            session()->flash('flash_message_type', 'alert-danger');

            return redirect()->back();

        } else {
            $logged_user = session('logged_user_id');

            $route = new Routes();
            $route->route_name = $request->routeName;
            if ($request->routeDescription != '') {
                $route->route_description = $request->routeDescription;

            } else {
                $route->route_description = null;
            }

            $route->is_active = STATIC_DATA_MODEL::$Active;
            $route->created_at = Carbon::now();
            $route->updated_at = Carbon::now();
            $route->um_user_id = $logged_user;

            $routesaved = $route->save();

            //Get last record user login
            $lastRouteId = \DB::table('cm_routes')->latest()->first();

            if (!$routesaved) {
                session()->flash('message', 'Route Save Failed');
                session()->flash('flash_message_type', 'alert-danger');

                return redirect()->back();
            } else {

                //Save user activity
                $userActivity = new UserActivityManagementController();
                $userActivity->saveActivity(STATIC_DATA_MODEL::$insert, "New Route " . $lastRouteId->id . " Saved.");

                session()->flash('message', 'Route Save success');
                session()->flash('flash_message_type', 'alert-success');

                return redirect()->back();
            }

        }

    }

    public function deleteRoute($id)
    {
        $route = Routes::find($id);

        if ($route->is_active == STATIC_DATA_MODEL::$Active) {
            // if residential status is active
            $routeStatusUpdate = Routes::find($id);
            $routeStatusUpdate->is_active = STATIC_DATA_MODEL::$Inactive;

            $routeStatusUpdate->save();

            //Save user activity
            $userActivity = new UserActivityManagementController();
            $userActivity->saveActivity(STATIC_DATA_MODEL::$update, "Inactive Route Status " . $id);

            session()->flash('message', 'Route Status Deactivate Success!!');
            session()->flash('flash_message_type', 'alert-success');
        } else {
            // if residential status is inactive
            $routeStatusUpdate = Routes::find($id);
            $routeStatusUpdate->is_active = STATIC_DATA_MODEL::$Active;

            $routeStatusUpdate->save();

            //Save user activity
            $userActivity = new UserActivityManagementController();
            $userActivity->saveActivity(STATIC_DATA_MODEL::$update, "active Route Status " . $id);

            session()->flash('message', 'Route Status Active Success!!');
            session()->flash('flash_message_type', 'alert-success');
        }

        return redirect()->back();
    }

    public function loadRouteData(Request $request)
    {
        $routeData = Routes::find($request->routeId);

        return view('vehicle.arrangeVehicle.route.ajaxRoute.ajaxLoadRouteDataToModal', compact('routeData'));
    }

    public function updateRoute(Request $request)
    {
        $this->validate($request, [
            'routeNameUpdate' => 'required',

        ]);

        $checkRoute = Routes::find($request->routeHiddenId);

        $routerStatus = true;
        if (Routes::where('route_name', request('routeNameUpdate'))->exists()) {

            if ($checkRoute->route_name == $request->routeNameUpdate) {
                $routerStatus = true;
            } else {
                $routerStatus = false;
                session()->flash('message', 'Route Name already exits!!');
                session()->flash('flash_message_type', 'alert-danger');

                return redirect()->back();
            }

        }

        if ($routerStatus) {
            $routeUpdate = Routes::find($request->routeHiddenId);
            $routeUpdate->route_name = $request->routeNameUpdate;

            $routeUpdate->route_description = $request->routeDescriptionUpdate;

            $routeUpdate->updated_at = Carbon::now();

            $routeUpdatesaved = $routeUpdate->save();

            if (!$routeUpdatesaved) {
                session()->flash('message', 'Route Update Failed');
                session()->flash('flash_message_type', 'alert-danger');

                return redirect()->back();
            } else {

//Save user activity
                $userActivity = new UserActivityManagementController();
                $userActivity->saveActivity(STATIC_DATA_MODEL::$update, "Update Route " . $request->routeHiddenId . " Updated.");

                session()->flash('message', 'Route Update success');
                session()->flash('flash_message_type', 'alert-success');

                return redirect()->back();
            }

        }
    }

    public function searchShopsToRoute(Request $request)
    {
        $shops = Customer::where([['cm_routes_id', $request->route], ['is_active', STATIC_DATA_MODEL::$Active]])->get();
        return view('vehicle.arrangeVehicle.route.ajaxRoute.ajaxLoadCustomersToRoute', compact('shops'));

    }

}

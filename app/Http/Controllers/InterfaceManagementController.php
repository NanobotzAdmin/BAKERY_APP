<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Interfaces;
use App\InterfaceTopics;
use App\STATIC_DATA_MODEL;
use App\InterfaceComponents;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class InterfaceManagementController extends Controller
{

    public function adminInterfaceManagementIndex()
    {
        $interfaceTopics = InterfaceTopics::all();
        $interfaces = Interfaces::all();
        $interfaceComponents = InterfaceComponents::all();

        return view('interfaceManagement.interfacemanagement', compact('interfaceTopics', 'interfaces', 'interfaceComponents'));
    }

    // save interface topics
    public function saveInterfaceTopic(Request $request)
    {
        $this->validate($request, [
            'InterfaceTopic_Name' => 'required',
            'InterfaceTopic_Icon' => 'required',
            'InterfaceTopic_Section' => 'required',
        ]);

        if (InterfaceTopics::where('topic_name', request('InterfaceTopic_Name'))->exists()) {
            session()->flash('message', 'Interface topic name already exist!!');
            session()->flash('flash_message_type', 'alert-danger');
            return redirect()->back();

        } else {
            // logged user id
            $logged_user = session('logged_user_id');

            $interfaceTopic = new InterfaceTopics();
            $interfaceTopic->topic_name = $request->InterfaceTopic_Name;
            $interfaceTopic->menu_icon = $request->InterfaceTopic_Icon;
            $interfaceTopic->section_class = $request->InterfaceTopic_Section;
            $interfaceTopic->remark = null;
            $interfaceTopic->created_at = Carbon::now();
            $interfaceTopic->updated_at = Carbon::now();
            $interfaceTopic->created_by = $logged_user;
            $interfaceTopicSave = $interfaceTopic->save();

            if (!$interfaceTopicSave) {
                session()->flash('message', 'Interface topic save Failed!!');
                session()->flash('flash_message_type', 'alert-danger');

                return redirect()->back();
            } else {
                $lastInterfaceTopic = DB::table('pm_interface_topic')->latest()->first();

                //Save user activity
                $userActivity = new UserActivityManagementController();
                $userActivity->saveActivity(STATIC_DATA_MODEL::$insert, "New interface topic- " . $lastInterfaceTopic->id . " Saved.");

                session()->flash('message', 'Interface topic save Success!!');
                session()->flash('flash_message_type', 'alert-success');

                return redirect()->back();
            }
        }
    }


    // save interface
    public function saveInterface(Request $request)
    {
        $this->validate($request, [
            'topic' => 'required|not_in:0',
            'interface_name' => 'required',
            'interface_url' => 'required',
            'interface_icon' => 'required',
            'interface_title' => 'required',
        ]);

        if (Interfaces::where('interface_name', request('interface_name'))->exists()) {
            session()->flash('message', 'Interface name already exist!!');
            session()->flash('flash_message_type', 'alert-danger');
            return redirect()->back();

        } else {
            // logged user id
            $logged_user = session('logged_user_id');

            $interfaces = new Interfaces();
            $interfaces->interface_name = $request->interface_name;
            $interfaces->path = $request->interface_url;
            $interfaces->icon_class = $request->interface_icon;
            $interfaces->tile_class = $request->interface_title;
            $interfaces->remark = null;
            $interfaces->created_at = Carbon::now();
            $interfaces->updated_at = Carbon::now();
            $interfaces->pm_interface_topic_id = $request->topic;
            $interfaces->created_by = $logged_user;
            $interfaceSave = $interfaces->save();

            if (!$interfaceSave) {
                session()->flash('message', 'Interface save Failed!!');
                session()->flash('flash_message_type', 'alert-danger');

                return redirect()->back();
            } else {
                $lastInterface = DB::table('pm_interfaces')->latest()->first();

                // Save user activity
                $userActivity = new UserActivityManagementController();
                $userActivity->saveActivity(STATIC_DATA_MODEL::$insert, "New interface - " . $lastInterface->id . " Saved.");

                session()->flash('message', 'Interface save Success!!');
                session()->flash('flash_message_type', 'alert-success');

                return redirect()->back();
            }
        }
    }


    // save component
    public function saveInterfaceComponent(Request $request)
    {
        $this->validate($request, [
            'component_topic' => 'required|not_in:0',
            'component_interface' => 'required|not_in:0',
            'component_name' => 'required',
            'component_id' => 'required',
        ]);

        if (InterfaceComponents::where('components_name', request('component_name'))->exists()) {
            session()->flash('message', 'Interface component Name already exist!!');
            session()->flash('flash_message_type', 'alert-danger');
            return redirect()->back();

        } else if (InterfaceComponents::where('component_id', request('component_id'))->exists()) {
            session()->flash('message', 'Interface component Id already exist!!');
            session()->flash('flash_message_type', 'alert-danger');
            return redirect()->back();

        } else {
            // logged user id
            $logged_user = session('logged_user_id');

            $interfaceComponent = new InterfaceComponents();
            $interfaceComponent->components_name = $request->component_name;
            $interfaceComponent->component_id = $request->component_id;
            $interfaceComponent->is_active = STATIC_DATA_MODEL::$Active;
            $interfaceComponent->created_at = Carbon::now();
            $interfaceComponent->updated_at = Carbon::now();
            $interfaceComponent->pm_interfaces_id = $request->component_interface;
            $interfaceComponent->created_by = $logged_user;

            $interfaceComponentSave = $interfaceComponent->save();

            if (!$interfaceComponentSave) {
                session()->flash('message', 'Interface Component save Failed!!');
                session()->flash('flash_message_type', 'alert-danger');
                return redirect()->back();

            } else {
                $lastInterfaceComponent = DB::table('pm_interface_components')->latest()->first();
                //Save user activity
                $userActivity = new UserActivityManagementController();
                $userActivity->saveActivity(STATIC_DATA_MODEL::$insert, "New interface Component - " . $lastInterfaceComponent->id . " Saved.");

                session()->flash('message', 'Interface Component save Success!!');
                session()->flash('flash_message_type', 'alert-success');
                return redirect()->back();
            }
        }
    }


    public function loadInterfaces(Request $request)
    {
        $intefaceCompo = Interfaces::where('pm_interface_topic_id', $request->topicId)->get();
        return compact('intefaceCompo');
    }

    // public function loadInterfaceDataToModal(Request $request)
    // {
    //     if ($request->section == "interfaceTopic") {
    //         $interfaceTopic_OBJ = InterfaceTopics::find($request->id);
    //         return view('interfaceManagement.ajaxInterface.ajaxInterfaceLoadDataToModal', compact('interfaceTopic_OBJ'));
    //     }
    // }



    // load Interface Topic details to modal
    public function loadInterfaceTopicDetailsToModal(Request $request)
    {
        if ($request->section == "interfaceTopic") {
            $interfaceTopic_OBJ = InterfaceTopics::find($request->id);
            return view('interfaceManagement.ajaxInterface.ajaxLoadInterfaceTopicDataToModal', compact('interfaceTopic_OBJ'));
        }
    }

    // load Interface details to modal
    public function loadInterfaceDetailsToModal(Request $request)
    {
        if ($request->section == "interface") {
            $interface_OBJ = Interfaces::find($request->id);
            $interfaceTopic_list = InterfaceTopics::get();
            return view('interfaceManagement.ajaxInterface.ajaxLoadInterfaceDataToModal', compact('interface_OBJ', 'interfaceTopic_list'));
        }
    }

    // load Interface Component details to modal
    public function loadInterfaceComponentDetailsToModal(Request $request)
    {
        if ($request->section == "interfaceComponent") {
            $interfaceComponent_OBJ = InterfaceComponents::find($request->id);
            $interfaceTopic_list = InterfaceTopics::get();
            return view('interfaceManagement.ajaxInterface.ajaxLoadInterfaceComponentDataToModal', compact('interfaceComponent_OBJ', 'interfaceTopic_list'));
        }
    }


    // Update Interface Topic
    public function updateInterfaceTopic(Request $request)
    {
        // Validations
        $validator = Validator::make($request->all(), [
            'topic_id' => 'required|exists:pm_interface_topic,id', // Check field is required & also checks the record exists in DB table
            'topic_name' => 'required|string|max:100|unique:pm_interface_topic,topic_name,' . $request->topic_id, // Ensures topic_name is unique except for the current topic_id
            'menu_icon' => 'required|string|max:100',
            'section_class' => 'required|string|max:100'
        ], [
            'topic_id.required' => 'Selected Topic id is missing!',
            'topic_id.exists' => 'The selected topic does not exist in the database.',

            'topic_name.required' => 'Please enter a Topic Name.',
            'topic_name.string' => 'Topic Name must be a valid string.',
            'topic_name.max' => 'Topic Name must not exceed 100 characters.',
            'topic_name.unique' => 'Entered Topic Name has already been taken.', // error message for uniqueness check

            'menu_icon.required' => 'Please fill the Menu Icon field.',
            'menu_icon.string' => 'The Menu Icon must be a valid string.',
            'menu_icon.max' => 'The Menu Icon must not exceed 100 characters.',

            'section_class.required' => 'Please fill the Section Class field.',
            'section_class.string' => 'The Section Class must be a valid string.',
            'section_class.max' => 'The Section Class must not exceed 100 characters.'
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
                'type' => 'validation'
            ], 422);
        }

        try {
            $logged_user = session('logged_user_id');
            $logged_user_OBJ = User::find($logged_user);

            // Find InterfaceTopic by id
            $InterfaceTopic_OBJ = InterfaceTopics::findOrFail($request->topic_id);

            // Update attributes
            $InterfaceTopic_OBJ->topic_name = $request->topic_name;
            $InterfaceTopic_OBJ->menu_icon = $request->menu_icon;
            $InterfaceTopic_OBJ->section_class = $request->section_class;
            $InterfaceTopic_OBJ->updated_at = Carbon::now();

            // Save the updates
            $InterfaceTopic_OBJ->save();

            // Save user activity
            $userActivity = new UserActivityManagementController();
            $userActivity->saveActivity(STATIC_DATA_MODEL::$update, "Interface Topic ID: {$InterfaceTopic_OBJ->id} was Updated by USER ID: {$logged_user_OBJ->id} ({$logged_user_OBJ->first_name} {$logged_user_OBJ->last_name}).");

            // Return success response
            return response()->json([
                'status' => 'success',
                'message' => 'Interface Topic successfully updated.',
                'type' => 'success'
            ]);

        } catch (\Exception $e) {
            // Log the error or handle exceptions as needed
            return response()->json([
                'status' => 'error',
                'message' => 'Interface Topic update failed. ' . $e->getMessage(),
                'type' => 'exception'
            ], 500);
        }
    }

    // Update Interface
    public function updateInterface(Request $request)
    {
        // Validations
        $validator = Validator::make($request->all(), [
            'interface_id' => 'required|exists:pm_interfaces,id', // Check field is required & also checks the record exists in DB table
            'topic_id' => 'required|integer|not_in:0', // Ensures topic_id is required, an integer, and not "0"
            'interface_name' => 'required|string|max:150|unique:pm_interfaces,interface_name,' . $request->interface_id, // Ensures interface_name is unique except for the current interface_id
            'interface_URL' => 'required|string|max:150|unique:pm_interfaces,path,' . $request->interface_id, // Ensures path is unique except for the current interface_id
            'icon_class' => 'required|string|max:100',
            'tile_class' => 'required|string|max:20'
        ], [
            'interface_id.required' => 'Selected Interface id is missing!',
            'interface_id.exists' => 'The selected Interface does not exist.',

            'topic_id.not_in' => 'Please select a Topic.', // Custom message for 'not_in:0'
            'topic_id.required' => 'Interface Topic is required.',
            'topic_id.integer' => 'Interface Topic must be a valid integer.',

            'interface_name.required' => 'Please enter a Interface Name.',
            'interface_name.string' => 'Interface Name must be a string.',
            'interface_name.max' => 'Interface Name must not exceed 150 characters.',
            'interface_name.unique' => 'Entered Interface Name has already been taken.', // error message for uniqueness check

            'interface_URL.required' => 'Please enter Interface URL.',
            'interface_URL.string' => 'Interface URL must be a string.',
            'interface_URL.max' => 'Interface URL must not exceed 150 characters.',
            'interface_URL.unique' => 'Entered Interface URL has already been taken.', // error message for uniqueness check

            'icon_class.required' => 'Please enter a Icon Class.',
            'icon_class.string' => 'Icon Class must be a string.',
            'icon_class.max' => 'Icon Class must not exceed 100 characters.',

            'tile_class.required' => 'Please enter a Title Class.',
            'tile_class.string' => 'Title Class must be a string.',
            'tile_class.max' => 'Title Class must not exceed 20 characters.',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
                'type' => 'validation'
            ], 422);
        }

        try {
            $logged_user = session('logged_user_id');
            $logged_user_OBJ = User::find($logged_user);

            // Find InterfaceTopic by id
            $Interface_OBJ = Interfaces::findOrFail($request->interface_id);

            // Update attributes
            $Interface_OBJ->pm_interface_topic_id = $request->topic_id;
            $Interface_OBJ->interface_name = $request->interface_name;
            $Interface_OBJ->path = $request->interface_URL;
            $Interface_OBJ->icon_class = $request->icon_class;
            $Interface_OBJ->tile_class = $request->tile_class;
            $Interface_OBJ->updated_at = Carbon::now();

            // Save the updates
            $Interface_OBJ->save();

            // Save user activity
            $userActivity = new UserActivityManagementController();
            $userActivity->saveActivity(STATIC_DATA_MODEL::$update, "Interface ID: {$Interface_OBJ->id} was Updated by USER ID: {$logged_user_OBJ->id} ({$logged_user_OBJ->first_name} {$logged_user_OBJ->last_name}).");

            // Return success response
            return response()->json([
                'status' => 'success',
                'message' => 'Interface successfully updated.',
                'type' => 'success'
            ]);

        } catch (\Exception $e) {
            // Log the error or handle exceptions as needed
            return response()->json([
                'status' => 'error',
                'message' => 'Interface update failed. ' . $e->getMessage(),
                'type' => 'exception'
            ], 500);
        }
    }

    // Update Interface Component
    public function updateInterfaceComponent(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'component_id' => 'required|exists:pm_interface_components,id', // Check field is required & also checks the record exists in DB table
            'topic_id' => 'required|integer|not_in:0', // Ensures topic_id is required, an integer, and not "0"
            'interface_id' => 'required|integer|not_in:0', // Ensures interface_id is required, an integer, and not "0"
            'interface_component_name' => 'required|string|max:100|unique:pm_interface_components,components_name,' . $request->component_id, // Ensures Component Name is unique except for the current component_id record
            'interface_component_id' => 'required|string|max:100|unique:pm_interface_components,component_id,' . $request->component_id // Ensures Component ID is unique except for the current component_id record
        ], [
            'component_id.required' => 'Selected Component id is missing!',
            'component_id.exists' => 'The selected component does not exist.',

            'topic_id.required' => 'Interface Topic ID is required.',
            'topic_id.integer' => 'Interface Topic must be a valid integer.',
            'topic_id.not_in' => 'Please select an Interface Topic.', // Custom message for topic id is 'not_in:0'

            'interface_id.required' => 'Interface ID is required.',
            'interface_id.integer' => 'Interface ID must be a valid integer.',
            'interface_id.not_in' => 'Please select an Interface.', // Custom message for interface id is 'not_in:0'

            'interface_component_name.required' => 'Please enter a Component Name.',
            'interface_component_name.string' => 'Component Name must be a valid string.',
            'interface_component_name.max' => 'Component Name must not exceed 100 characters.',
            'interface_component_name.unique' => 'Entered Component Name has already been taken.', // error message for uniqueness check

            'interface_component_id.required' => 'Please enter a Component ID.',
            'interface_component_id.string' => 'Component ID must be a valid string.',
            'interface_component_id.max' => 'Component ID must not exceed 100 characters.',
            'interface_component_id.unique' => 'Entered Interface Component ID has already been taken.', // error message for uniqueness check
        ]);


        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
                'type' => 'validation'
            ], 422);
        }

        try {
            $logged_user = session('logged_user_id');
            $logged_user_OBJ = User::find($logged_user);

            // Find InterfaceTopic by id
            $InterfaceComponent_OBJ = InterfaceComponents::findOrFail($request->component_id);

            // Update attributes
            $InterfaceComponent_OBJ->components_name = $request->interface_component_name;
            $InterfaceComponent_OBJ->component_id = $request->interface_component_id;
            $InterfaceComponent_OBJ->pm_interfaces_id = $request->interface_id;
            $InterfaceComponent_OBJ->updated_at = Carbon::now();

            // Save the updates
            $InterfaceComponent_OBJ->save();

            // Save user activity
            $userActivity = new UserActivityManagementController();
            $userActivity->saveActivity(STATIC_DATA_MODEL::$update, "Interface Component ID: {$InterfaceComponent_OBJ->id} was Updated by USER ID: {$logged_user_OBJ->id} ({$logged_user_OBJ->first_name} {$logged_user_OBJ->last_name}).");

            // Return success response
            return response()->json([
                'status' => 'success',
                'message' => 'Interface Component successfully updated.',
                'type' => 'success'
            ]);

        } catch (\Exception $e) {
            // Log the error or handle exceptions as needed
            return response()->json([
                'status' => 'error',
                'message' => 'Interface Component update failed. ' . $e->getMessage(),
                'type' => 'exception'
            ], 500);
        }
    }

}

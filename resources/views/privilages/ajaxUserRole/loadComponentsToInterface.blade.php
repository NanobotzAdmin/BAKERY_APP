@foreach ($componentList as $components)


<?php

$compoHasUserUser =  \DB::table('pm_user_role_has_interface_components')
                        ->select('pm_user_role_has_interface_components.*')
                        ->where('pm_user_role_id', $userRole)
                        ->where('pm_interface_components_id', $components->id)
                        ->first();
?>

@if($compoHasUserUser!=null)
<div class="form-group row">
        <label class="col-sm-8 control-label">{{ $components->components_name }}
        </label>

        <label class="switch">
            <input type="checkbox" checked  onclick="saveDeleteUserRoleComponent({{ $components->id }},{{ $userRole }},'S')">
            <span class="slider round"></span>
        </label>
    </div>
    @else
    <div class="form-group row">
            <label class="col-sm-8 control-label">{{ $components->components_name }}
            </label>

            <label class="switch">
                <input type="checkbox" onclick="saveDeleteUserRoleComponent({{ $components->id }},{{ $userRole }},'N')">
                <span class="slider round"></span>
            </label>
        </div>
    @endif
    @endforeach

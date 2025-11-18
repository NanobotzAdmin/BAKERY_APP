@php
    $privilageId = \DB::table('pm_interfaces')
    ->select('pm_interfaces.id AS pageId','pm_interface_topic.id AS grupId')
    ->join('pm_interface_topic', 'pm_interfaces.pm_interface_topic_id', '=', 'pm_interface_topic.id')
    ->where('pm_interfaces.path','adminPrivilageManagement')
    ->first();
@endphp

@extends('layout', ['pageId' => $privilageId->pageId ,'grupId' => $privilageId->grupId ])

@section('content')
<style>
    .switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
    }

    input:checked+.slider {
        background-color: #21f37e;
    }

    input:focus+.slider {
        box-shadow: 0 0 1px #21f37e;
    }

    input:checked+.slider:before {
        -webkit-transform: translateX(26px);
        -ms-transform: translateX(26px);
        transform: translateX(26px);
    }

    /* Rounded sliders */
    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
    }
</style>


{{-- Component Switch CSS --}}
<style>
    /* The switch container */
    .switch {
      position: relative;
      display: inline-block;
      width: 40px;  /* Reduced width */
      height: 20px; /* Reduced height */
    }

    /* Hide the default checkbox */
    .switch input {
      opacity: 0;
      width: 0;
      height: 0;
    }

    /* The slider */
    .slider {
      position: absolute;
      cursor: pointer;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: #ccc;
      transition: 0.4s;
      border-radius: 20px; /* Adjusted for new height */
    }

    .slider.round {
      border-radius: 20px; /* Adjusted for new height */
    }

    /* The circle (handle) inside the slider */
    .slider:before {
      position: absolute;
      content: "";
      height: 16px;   /* Reduced height */
      width: 16px;    /* Reduced width */
      left: 2px;      /* Adjusted position */
      bottom: 2px;    /* Adjusted position */
      background-color: white;
      transition: 0.4s;
      border-radius: 50%;
    }

    /* When the checkbox is checked - change background color */
    input:checked + .slider {
      background-color: #00ec7e; /* Set the 'on' color to ec9a00 */
    }

    /* When the checkbox is checked - move the circle */
    input:checked + .slider:before {
      transform: translateX(20px); /* Adjusted for new width */
    }

    /* Add a glowing effect when the slider is clicked and focused */
    input:checked:focus + .slider {
      box-shadow: 0 0 1px #00ec7e; /* Add glow color */
    }
</style>



<div class="">
    <input type="hidden" id="csrf_token" value="{{ csrf_token() }}">

    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2><b>Privilege Management</b></h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="/admindashboard">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <a>Admin Settings</a>
                </li>
                <li class="breadcrumb-item active">
                    <strong>Privilege Management</strong>
                </li>
            </ol>
        </div>
        <div class="col-lg-2">

        </div>
    </div>

    <div class="row wrapper wrapper-content">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="tabs-container">
                                <ul class="nav nav-tabs" role="tablist">
                                    <li><a class="nav-link active" data-toggle="tab" href="#tab-1"> User Role Has
                                            Permission</a></li>
                                    <li><a class="nav-link" data-toggle="tab" href="#tab-2">User Has Permission</a></li>
                                </ul>
                                <div class="tab-content">
                                    <div role="tabpanel" id="tab-1" class="tab-pane active">
                                        <div class="panel-body">

                                            <div class="row">
                                                <div class="col-xs-12 col-sm-4">
                                                    <div class="form-group">
                                                        <div class="col-xs-12">
                                                            <label class="control-label">Select User-Role</label>
                                                            <select class="select2_demo_3 form-control" id="userRoleselect" name="userRoleselect">
                                                                <option value="0" selected="" disabled="">-- Select One --</option>
                                                                @foreach ($userRoleList as $userRole)
                                                                <option value="{{ $userRole->id }}">{{ $userRole->user_role_name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xs-12 col-sm-4">
                                                    <div class="form-group">
                                                        <div class="col-xs-12">
                                                            <label class="control-label">Select Interface Topic</label>
                                                            <select class="select2_demo_3 form-control" id="interfaceTopicsSelect" name="interfaceTopicsSelect" onchange="loadInterfaces(this.value)">
                                                                <option value="0" selected="" disabled="">-- Select One --</option>
                                                                @foreach ($interfaceTopicList as $interfaceTopics)
                                                                <option value="{{ $interfaceTopics->id }}">
                                                                    {{ $interfaceTopics->topic_name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <br>
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="portlet light">
                                                        <div class="portlet-title">
                                                            <h3 class="caption-subject"
                                                                style="color: #008f75; font-weight: bold">Interfaces
                                                            </h3>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="portlet light">
                                                        <div class="portlet-title">
                                                            <h3 class="caption-subject"
                                                                style="color: #008f75; font-weight: bold">Components
                                                            </h3>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row" style="border-bottom: #888; border-bottom-style: ridge">
                                                <div class="col-xs-12 col-sm-6">
                                                    <div class="ibox">
                                                        <div class="ibox-content" id="userRoleInterfacesLoadingDiv">



                                                        </div>
                                                    </div>
                                                </div>


                                                <div class="col-xs-12 col-sm-6">
                                                    <div class="ibox">
                                                        <div class="ibox-content" id="userRoleComponentsLoadingDiv">

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div role="tabpanel" id="tab-2" class="tab-pane">
                                        <div class="panel-body">
                                            <div class="row">
                                                <div class="col-xs-12 col-sm-4">
                                                    <div class="form-group">
                                                        <div class="col-xs-12">
                                                            <label class="control-label">Select User-Role</label><br>
                                                            <select class="select2_demo_3 form-control" id="">
                                                                <option selected="" disabled="" value="">--Select--
                                                                </option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xs-12 col-sm-4">
                                                    <div class="form-group">
                                                        <div class="col-xs-12">
                                                            <label class="control-label">Select User</label><br>
                                                            <div id="">
                                                                <select class="select2_demo_3 form-control" id="">
                                                                    <option selected="" disabled="" value="">--Select--
                                                                    </option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xs-12 col-sm-4">
                                                    <div class="form-group">
                                                        <div class="col-xs-12">
                                                            <label class="control-label">Select Interface Topic</label><br>
                                                            <select class="select2_demo_3 form-control" id="">
                                                                <option selected="" disabled="" value="">--Select--
                                                                </option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <br>
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="portlet light">
                                                        <div class="portlet-title">
                                                            <h3 class="caption-subject"
                                                                style="color: #008f75; font-weight: bold">Interfaces
                                                            </h3>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="portlet light">
                                                        <div class="portlet-title">
                                                            <h3 class="caption-subject"
                                                                style="color: #008f75; font-weight: bold">Components
                                                            </h3>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row" style="border-bottom: #888; border-bottom-style: ridge">
                                                <div class="col-xs-12 col-sm-6">
                                                    <div class="ibox">
                                                        <div class="ibox-content">
                                                            <form class="form">
                                                                <div class=" form-group row">
                                                                    <div class="col-sm-9"
                                                                        style="border-bottom: #cccccc; border-bottom-style: ridge">
                                                                        <label class=" control-label"
                                                                            style="margin-top: 10px;">Interface 21</label>
                                                                    </div>
                                                                    <div class="col-sm-1">
                                                                        <button type="button"
                                                                            class="btn btn-success btn-sm">View</button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-xs-12 col-sm-6">
                                                    <div class="ibox">
                                                        <div class="ibox-content">
                                                            <div class="form-group row">
                                                                <label class="col-sm-8 control-label">Interface C
                                                                </label>

                                                                <label class="switch">
                                                                    <input type="checkbox">
                                                                    <span class="slider round"></span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer')
<script>
    $(".select2_demo_3").select2({
        placeholder: "Select a state",
        allowClear: true
    });


    $(document).ready(function() {
        $('.dataTables-example').DataTable({
            pageLength: 10,
            responsive: true,
            dom: '<"html5buttons"B>lTfgitp',
            buttons: []
        });
    });
///////////////////// USER ROLE /////////////////////////


     function loadInterfaces(interfaceTopic) {
        var csrf_token = $("#csrf_token").val();
        var userRole = $('#userRoleselect option:selected').val();

        if (userRole == 0) {
            swal("Sorry!", "Select User Role!", "warning");
        } else {
            jQuery.ajax({
                url: "{{ url('/loadInterfacesToInterfaceTopics') }}",
                type: "POST",
                data: {
                    "_token": csrf_token,
                    "InterfaceTopic": interfaceTopic,
                    "userRole" :userRole,
                },
                beforeSend: function() {
                    showLder();
                },
                complete: function() {
                },
                error: function(data) {
                },
                success: function(data) {
                    hideLder();
                    $('#userRoleInterfacesLoadingDiv').html(data);
                }
            });
        }
    }


    function loadInterfaceComponents(interfaceId,userRoleId) {
        var csrf_token = $("#csrf_token").val();
        $(".interface-view-btn").each(function () {
            $(this).removeClass();
            $(this).addClass("btn btn-info btn-xs interface-view-btn");
        });

        $('#intbtn'+interfaceId).addClass('btn btn-warning btn-xs interface-view-btn');

        jQuery.ajax({
            url: "{{ url('/loadComponentsToInteface') }}",
            type: "POST",
            data: {
                "_token": csrf_token,
                "interfaceId": interfaceId,
                "userRoleId" :userRoleId,
            },
            beforeSend: function() {
                showLder();
            },
            complete: function() {
            },
            error: function(data) {
            },
            success: function(data) {
                hideLder();
                $('#userRoleComponentsLoadingDiv').html(data);
            }
        });
    }



    function saveDeleteUserRoleComponent(componentID,userRoleId,checkStatus) {
        var csrf_token = $("#csrf_token").val();
        jQuery.ajax({
            url: "{{ url('/saveDeleteUserRoleComponent') }}",
            type: "POST",
            data: {
                "_token": csrf_token,
                "componentID": componentID,
                "userRoleId" :userRoleId,
                "checkStatus":checkStatus,
            },
            beforeSend: function() {
                showLder();
            },
            complete: function() {
            },
            error: function(data) {
            },
            success: function(data) {
                hideLder();
                loadInterfaceComponents(data.roleId,data.userRole);

                swal({
                    title: "Success!",
                    text: data.msg,
                    type: "success",
                    timer: 1000,
                    showConfirmButton: false
                });
            }
        });
    }
//////////// USER ROLE END /////////////////////
</script>

@endsection

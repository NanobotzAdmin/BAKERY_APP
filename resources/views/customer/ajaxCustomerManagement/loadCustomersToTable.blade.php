@php
    $Nanobots_Admin = 1;
    $System_Admin = 2;
    $SalesRep = 3;
    $Driver = 4;
    $Manager = 5;
@endphp

<div class="ibox-title">
    <h5 style="font-family: 'Sawarabi Gothic', sans-serif; letter-spacing: 1.5px;">Manage Customers
        @if ($GG == 1)
            on Route ➠ <span style="color: #ff9100;">{{ $assignedRoute->route_name }}</span>
        @endif
    </h5>
</div>

<div class="ibox-content">
    <div class="row">
        <div class="col-sm-1">
            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#createCustomer">Create Customer</button>
        </div>
        &nbsp;&nbsp;
        <div class="col-sm-1">
            <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#addStoreRackCount" onclick="viewStoreRackModel()">Add Store Rack Count</button>
        </div>
    </div>
</div>

<div class="ibox-content">
    <div class="table-responsive">
        <table class="table table-bordered table-hover dataTables-example" style="font-family: 'Lato', sans-serif;">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Contact No</th>
                    <th>Contact Person</th>
                    <th style="min-width: 80px;">Created Date</th>
                    <th>Rack Count</th>
                    <th>Max Credit Bills</th>
                    <th>Max Credit Bill Availability</th>
                    <th>Status</th>
                    <th>Location</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($customerList as $Customer)
                    @php
                        $dateCus = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $Customer->created_at)->format('Y-m-d');
                        $dateNow = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', \Carbon\Carbon::now())->format('Y-m-d');
                        $diff = strtotime($dateNow) - strtotime($dateCus);
                        $diffDates = abs(round($diff / 86400));
                        $rackcountTb = 0;
                        $rackCus = App\CustomerRack::where('cm_customers_id', $Customer->id)->first();

                        if (empty($rackCus)) {
                            $rackcountTb = 0;
                        } else {
                            $rackcountTb = $rackCus->rack_count;
                        }
                    @endphp

                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $Customer->customer_name }} &nbsp;&nbsp; @if ($diffDates < 30) <span class="badge badge-pill badge-primary" style="font-size: 13px">{{ $diffDates }} Days Ago</span> @endif </td>
                        <td>{{ $Customer->address }}</td>
                        <td>{{ $Customer->contact_number }}</td>
                        <td>{{ $Customer->contact_person }}</td>
                        <td>{{ $dateCus }}</td>
                        <td>{{ $rackcountTb }}</td>
                        <td>{{ $Customer->max_credit_bills }}</td>
                        <td>{{ $Customer->max_credit_bill_availability }}</td>
                        @if ($Customer->is_active == 1)
                            <td style="min-width: 90px; color: #1ab394; text-align: center;"><span class="badge" style="color: #28a745; background-color: #e2f5e6;">Active</span></td>
                        @else
                            <td style="min-width: 90px; color: #e70000; text-align: center;"><span class="badge" style="color: #dc3545; background-color: #fceff0;">Inactive</span></td>
                        @endif
                        <td style="text-align: center;">
                            @if($Customer->location_link)
                                <a href="{{ $Customer->location_link }}" target="_blank">View Map <i class="fa fa-external-link-square"></i></a>
                            @else
                                <span style="font-size: 10px; font-style: italic;">N/A</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group">
                                {{-- UPDATE BUTTON --}}
                                @if ($LoggedUser->pm_user_role_id == $Nanobots_Admin || $LoggedUser->pm_user_role_id == $System_Admin || $LoggedUser->pm_user_role_id == $SalesRep || $LoggedUser->pm_user_role_id == $Manager)
                                <button type="button" class="btn btn-outline-warning btn-xs" data-toggle="modal" data-target="#updateUser" onclick="showCustomerUpdateModal({{ $Customer->id }})"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>&nbsp; Edit</button>
                                &nbsp;
                                @endif

                                {{-- UPDATE RACK BUTTON --}}
                                @if ($LoggedUser->pm_user_role_id == $Nanobots_Admin || $LoggedUser->pm_user_role_id == $System_Admin || $LoggedUser->pm_user_role_id == $Manager)
                                <button type="button" class="btn btn-outline-info btn-xs" data-target="#updateCustomerRack" data-toggle="modal" onclick="viewCustomerRackModel({{ $Customer->id }})"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>&nbsp; Update Rack Count</button>
                                &nbsp;
                                @endif

                                {{-- ACTIVE/DEACTIVE BUTTON --}}
                                @if ($LoggedUser->pm_user_role_id == $Nanobots_Admin || $LoggedUser->pm_user_role_id == $System_Admin || $LoggedUser->pm_user_role_id == $Manager)
                                    @if ($Customer->is_active == App\STATIC_DATA_MODEL::$Active)
                                        <button type="button" class="btn btn-outline-danger btn-xs" onclick="customerStatusChange({{ $Customer->id }})">Deactivate</button>
                                    @else
                                        <button type="button" class="btn btn-outline-success btn-xs" onclick="customerStatusChange({{ $Customer->id }})">&nbsp; Activate&nbsp;&nbsp;</button>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
    $('.dataTables-example').DataTable({
        pageLength: 25,
        responsive: true,
        dom: '<"html5buttons"B>lTfgitp',
        buttons: []
    });

    $(document).ready(function() {
        hideLder(); // hide the loader gif after ajax totally completed
    });
</script>

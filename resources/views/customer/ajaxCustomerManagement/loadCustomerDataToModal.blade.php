@php
    $Nanobots_Admin = 1;
    $System_Admin = 2;
    $SalesRep = 3;
    $Driver = 4;
    $Manager = 5;
@endphp

<form action="updateCustomer" method="POST">
        {{ csrf_field() }}
<div class="modal-header">
        <h4 class="modal-title" id="exampleModalLabel">Update Customer</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="modal-body">
        <input type="hidden" value="{{ $customer->id }}" name="MODALcusUpdateId"/>
        {{-- USER ROLE RESTRICTION --}}
        @if ($LoggedUser->pm_user_role_id == $Nanobots_Admin || $LoggedUser->pm_user_role_id == $System_Admin || $LoggedUser->pm_user_role_id == $Manager)
        <div style="">
        @else
        <div style="display: none">
        @endif
            <div class="form-group">
                <label for="MODAL_NAME"> Name</label>
                <input type="text" class="form-control form-control-sm" id="MODAL_NAME" value="{{ $customer->customer_name }}" name="MODAL_NAME" autocomplete="off">
            </div>

            {{-- USER ROLE RESTRICTION --}}
            @if ($LoggedUser->pm_user_role_id == 1 || $LoggedUser->pm_user_role_id == 2)
                <div class="form-group">
                    <label for="MODAL_ADDDRESS">Address</label>
                    <input type="text" class="form-control form-control-sm" id="MODAL_ADDDRESS" value="{{ $customer->address }}" name="MODAL_ADDDRESS" autocomplete="off">
                </div>
                <div class="form-group">
                    <label for="MODAL_CONTACT">Contact Number</label>
                    <input type="text" class="form-control form-control-sm" id="MODAL_CONTACT" value="{{ $customer->contact_number }}" name="MODAL_CONTACT" autocomplete="off">
                </div>
            @else
                <div class="form-group" style="display: none">
                    <label for="MODAL_ADDDRESS">Address</label>
                    <input type="text" class="form-control form-control-sm" id="MODAL_ADDDRESS" value="{{ $customer->address }}" name="MODAL_ADDDRESS" autocomplete="off">
                </div>
                <div class="form-group" style="display: none">
                    <label for="MODAL_CONTACT">Contact Number</label>
                    <input type="text" class="form-control form-control-sm" id="MODAL_CONTACT" value="{{ $customer->contact_number }}" name="MODAL_CONTACT" autocomplete="off">
                </div>
            @endif

            <div class="form-group">
                <label for="MODAL_CONTACT_PERSON">Contact Person</label>
                <input type="text" class="form-control form-control-sm" id="MODAL_CONTACT_PERSON" value="{{ $customer->contact_person }}" name="MODAL_CONTACT_PERSON" autocomplete="off">
            </div>
            <div class="form-group">
                <label for="MODAL_EMAIL">Email</label>
                <input type="email" class="form-control form-control-sm" id="MODAL_EMAIL" value="{{ $customer->email_address }}" name="MODAL_EMAIL" autocomplete="off">
            </div>

            @if ($LoggedUser->pm_user_role_id == 1 || $LoggedUser->pm_user_role_id == 2) {{-- Nanobotz Admin & System Admin --}}
            <div class="form-group">
            @else
            <div class="form-group" style="display: none;">
            @endif
                <label for="MODAL_Bill_Credit">Max Credit bill</label>
                <input type="number" class="form-control form-control-sm" id="MODAL_Bill_Credit" value="{{ $customer->max_credit_bills }}" name="MODAL_Bill_Credit" min="0" autocomplete="off">
            </div>

            <div class="form-group">
                <label for="MODAL_Credit_Amount">Max Credit Amount (Rs)</label>
                <input type="number" class="form-control form-control-sm" id="MODAL_Credit_Amount" value="{{ $customer->max_credit_amount }}" name="MODAL_Credit_Amount" autocomplete="off">
            </div>
            <div class="form-group">
                <label for="MODAL_Bill_Availability">Max Credit bill availability (days)</label>
                <input type="number" class="form-control form-control-sm" id="MODAL_Bill_Availability" value="{{ $customer->max_credit_bill_availability }}" name="MODAL_Bill_Availability" min="0" autocomplete="off">
            </div>
            <div class="form-group">
                <label for="routeUpdateModal">Route</label>
                <?php
                    $routes =App\Routes::all();
                ?>
                <select id="routeUpdateModal" name="routeUpdateModal" class="form-control form-control-sm">
                    @foreach ($routes as $route)
                        <option value="{{ $route->id }}" {{$customer->cm_routes_id == $route->id  ? 'selected' : ''}}>{{ $route->route_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <div class="form-group">
                <label for="MODAL_Latitude">Latitude</label>
                <input type="text" class="form-control form-control-sm" id="MODAL_Latitude" name="MODAL_Latitude" value="{{ $customer->latitude }}" autocomplete="off">
            </div>
            <div class="form-group">
                <label for="MODAL_Longitude">Longitude</label>
                <input type="text" class="form-control form-control-sm" id="MODAL_Longitude" name="MODAL_Longitude" value="{{ $customer->longitude }}" autocomplete="off">
            </div>
            <div class="form-group">
                <label for="MODAL_Location_Link">Map Location Link</label>
                <input type="text" class="form-control form-control-sm" id="MODAL_Location_Link" name="MODAL_Location_Link" value="{{ $customer->location_link }}" autocomplete="off">
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-xs btn-warning">Update Customer</button>
        <button type="button" class="btn btn-xs btn-secondary" data-dismiss="modal">Cancel</button>
    </div>
</form>

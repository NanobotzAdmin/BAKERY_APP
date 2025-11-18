<div class="modal-header">
    <h4 class="modal-title" id="exampleModalLabel">Update User</h4>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">
    <form>
        <input type="hidden" id="hid_va_tbl2" name="hid_va_tbl2" />

        <div class="form-group">
            <label for="MODAL_fname">First Name</label>
            <input type="text" class="form-control" id="MODAL_fname" name="MODAL_fname" value="{{ $userList->first_name }}" autocomplete="off">
        </div>

        <div class="form-group">
            <label for="MODAL_lname">Last Name</label>
            <input type="text" class="form-control" id="MODAL_lname" name="MODAL_lname" value="{{ $userList->last_name }}" autocomplete="off">
        </div>
        <div class="form-group">
            <label for="uRole">User Role</label>
            <select class="form-control" name="userRole" id="MODAL_UserRole">
                @foreach ($userRoles as $roles)
                    <option value="{{ $roles->id }}" {{ $userList->pm_user_role_id == $roles->id ? 'selected' : '' }} >{{ $roles->user_role_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="text" class="form-control" id="MODAL_password" name="MODAL_PASSWORD" value="{{ $userLogin->password }}" autocomplete="off">
        </div>
        <div class="form-check">
            @if ($userList->cash_allowed == 1)
                <input class="form-check-input chckboxSelectContact1 c7" type="checkbox" value="2" id="defaultCheck1" checked>
            @else
                <input class="form-check-input chckboxSelectContact1 c7" type="checkbox" value="2" id="defaultCheck1">
            @endif
            <label class="form-check-label" for="defaultCheck1">Cash Invoice Allowed</label>
        </div>

        <div class="form-check">
            @if ($userList->credit_allowed == 1)
                <input class="form-check-input chckboxSelectContact1 c7" type="checkbox" value="1" id="defaultCheck2" checked>
            @else
                <input class="form-check-input chckboxSelectContact1 c7" type="checkbox" value="1" id="defaultCheck2">
            @endif
            <label class="form-check-label" for="defaultCheck2">Credit Invoice Allowed</label>
        </div>
        <div class="form-check">
            @if ($userList->cheque_allowed == 1)
                <input class="form-check-input chckboxSelectContact1 c7" type="checkbox" value="3" id="defaultCheck3" checked>
            @else
                <input class="form-check-input chckboxSelectContact1 c7" type="checkbox" value="3" id="defaultCheck3">
            @endif
            <label class="form-check-label" for="defaultCheck3">Check Invoice Allowed</label>
        </div>
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-warning" onclick="updateUserData({{ $userList->id }})">Update</button>
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
</div>


<script>
    $(document).ready(function() {
        loadinvoiceValues2();
    });


    function loadinvoiceValues2() {
        var data = [];
        $(".chckboxSelectContact1").each(function(i, v) {
            if ($(v).prop('checked')) {
                data.push($(v).val());
            }
        });
        $("#hid_va_tbl2").val(JSON.stringify(data));
    }


    $(".chckboxSelectContact1").click(function() {
        $("#hid_va_tbl2").val("");
        var data = [];
        $(".chckboxSelectContact1").each(function(i, v) {
            if ($(v).prop('checked')) {
                data.push($(v).val());
            }
        });
        $("#hid_va_tbl2").val(JSON.stringify(data));

    });


    function updateUserData(userId) {
        var csrf_token = $("#csrf_token").val();
        jQuery.ajax({
            url: "{{ url('/updateUserdata') }}",
            type: "POST",
            data: {
                "_token": csrf_token,
                "userId": userId,
                "fname": $('#MODAL_fname').val(),
                "lname": $('#MODAL_lname').val(),
                "password": $('#MODAL_password').val(),
                "userRole": $("#MODAL_UserRole").val(),
                "hid_va_tbl": $("#hid_va_tbl2").val()
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
                if (data.msg == "error") {
                    swal("Sorry!", "User Update Error!", "warning");
                } else if (data.msg == "fullNameError") {
                    swal("Sorry!", "User Full name already exits!", "warning");
                } else if (data.msg == "success") {
                    swal("Success!", "User Update Success!", "success");
                    window.location = '/adminUserManagement';
                }
            }
        });
    }
</script>

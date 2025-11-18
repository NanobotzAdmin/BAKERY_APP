<label for="">Expire Date</label>
<div class="input-group date">
    <?php
        $today = Carbon\Carbon::now();
        $newDate = $today->addDays($proDetails->expire_in_days);
        $dateExpiry = $newDate->format('Y-m-d');
    ?>
    <input type="date" class="form-control" value="{{ $dateExpiry }}" name="expiryDate" id="expiryDate">
</div>

<script>
    // $(document).ready(function() {
    //     $('.dataTables-example').DataTable({
    //         pageLength: 10,
    //         responsive: true,
    //         dom: '<"html5buttons"B>lTfgitp',
    //         buttons: []
    //     });
    // });

    var mem = $('#data_1 .input-group.date').datepicker({
        todayBtn: "linked",
        keyboardNavigation: false,
        forceParse: false,
        calendarWeeks: true,
        autoclose: true
    });
</script>

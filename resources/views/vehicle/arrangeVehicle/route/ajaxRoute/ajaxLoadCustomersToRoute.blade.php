<style>
.table-hover tbody tr:hover {
    background-color: #faf6ec;
    color: #000;
    /* Light blue color - adjust as needed */
    transition: background-color 0.2s;
    /* Add a smooth transition effect */
}

.table th {
    text-align: center; /* Horizontally center the text */
    vertical-align: middle !important; /* Vertically center the text */
}
</style>

<table class="table table-bordered table-hover dataTables-example" id="routeTable" style="font-family: 'Lato', sans-serif;">
    <thead>
        <tr>
            <th>#</th>
            <th>Route</th>
            <th>Shop Name</th>
            <th>Shop Address</th>
            <th>Contact Person</th>
            <th>Contact Number</th>
            <th>Last Billed Date</th>
        </tr>
    </thead>
    <?php $id = 0; ?>
    @foreach ($shops as $shop)
        <?php $id++;
        $route = App\Routes::find($shop->cm_routes_id);
        $dateCus = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $shop->created_at)->format('Y-m-d');
        $dateNow = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', \Carbon\Carbon::now())->format('Y-m-d');
        $invoice = App\customerInvoices::where('cm_customers_id', $shop->id)
            ->orderBy('created_at', 'desc')
            ->first();

        $diff = strtotime($dateNow) - strtotime($dateCus);
        $diffDates = abs(round($diff / 86400));
        $lastBilDate = '';

        if (empty($invoice->created_at)) {
            $lastBilDate = '';
        } else {
            $lastBilDate = $invoice->created_at;
        }
        ?>

        <tr>
            <td>{{ $id }}</td>
            <td>{{ $route->route_name }}</td>
            <td><?php echo isset($shop->customer_name) ? $shop->customer_name : ' '; ?> &nbsp;&nbsp; @if ($diffDates < 30)
                    <span class="badge badge-pill badge-primary" style="font-size: 13px">{{ $diffDates }} Days
                        Ago</span>
                @endif
            </td>
            <td><?php echo isset($shop->address) ? $shop->address : ' '; ?></td>
            <td><?php echo isset($shop->contact_person) ? $shop->contact_person : ' '; ?></td>
            <td><?php echo isset($shop->contact_number) ? $shop->contact_number : ' '; ?></td>
            <td>{{ date('Y-m-d', strtotime($lastBilDate)) }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<script>
    $(document).ready(function() {
        $('#routeTable').DataTable({
            pageLength: 10,
            responsive: true,
            dom: '<"html5buttons"B>lTfgitp',
            buttons: [{
                    extend: 'excel',
                    title: 'RichVill_Route_Customers'
                },
                {
                    extend: 'pdf',
                    title: 'RichVill_Route_Customers'
                },
            ]
        });

        $(".select2_demo_3").select2({
            placeholder: "Select a state",
            allowClear: true
        });
    });
</script>

<style>
    /* --- Table CSS begins --- */
    .styl-table th:first-child {
        border-radius: 5px 0 0 0;
    }
    .styl-table th:last-child {
        border-radius: 0 5px 0 0;
    }
    .styl-table {
        border-collapse: collapse;
        margin: 25px 0;
        font-size: 12.5px;
        font-family:'Roboto Slab', serif;
        min-width: 400px;
    }
    .styl-table thead tr {
        background-color: #846f5d;
        color: #ffffff;
        text-align: left;
        font-size: 11px;
        font-weight: bold;
        font-family: 'Roboto Slab', serif;
        letter-spacing: 1px;
    }
    .styl-table th,
    .styl-table td {
        padding: 12px 15px;
    }
    .styl-table tbody tr {
        border-bottom: 1px solid #dddddd;
    }
    .styl-table tbody tr:nth-of-type(even) {
        background-color: #ffffff;
    }
    .styl-table tbody tr:last-of-type {
        border-bottom: 2px solid #846f5d;
    }
    .styl-table tbody tr:hover td {
        background-color: #fffcf1;
        color: #e47a00;
    }
</style>

<div class="modal-header">
    <h4 class="modal-title" id="exampleModalLabel">View Credit Bills</h4>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">
    <div class="table-responsive">
        <table class="table table-hover table-sm styl-table" id="TblAddProductTODelivery">
            <thead>
                <tr>
                    <th style="text-align: center;">#</th>
                    <th style="text-align: center;">Invoice NO</th>
                    <th style="text-align: center;">Amount</th>
                    <th style="text-align: center;">Paid Amount</th>
                    <th style="text-align: center;">Payment Status</th>
                    {{-- <th>Action</th> --}}
                </tr>
            </thead>
            <?php $id = 0; ?>
            <tbody>
                @foreach ($invoiceCustomer as $invoices)
                    <tr>
                        <?php
                        $id++;
                        ?>
                        <td>{{ $id }}</td>
                        <td>{{ $invoices->invoice_number }}</td>
                        <td style="text-align: right;">{{ number_format($invoices->net_price, 2) }}</td>
                        <td style="text-align: right;">{{ number_format($invoices->total_amout_paid, 2) }}</td>

                        @if ($invoices->invoice_status == 0)
                            <td style="text-align: center; color: #d30000">Pending</td>
                        @else
                            <td style="text-align: center; color: #00a210">Completed</td>
                        @endif
                        {{-- <td><button onclick='$(this).parent().parent().remove();' type='button' class='btn btn-sm btn-danger' value='Remove'><span class='fa fa-remove'>Remove</span></button></td> --}}
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="modal-footer">

</div>

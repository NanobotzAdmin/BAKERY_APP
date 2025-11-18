<div class="form-group col-md-4">
    <label>Invoice</label>
    <select class="select2_demo_3 form-control" id="invoiceCombo" onchange="loadInvoiceData()">
        <option value="0">-- Select One --</option>
        @foreach ($invoices as $invoice)
            <option value="{{ $invoice->id }}">{{ $invoice->invoice_number }}</option>
        @endforeach
    </select>
</div>


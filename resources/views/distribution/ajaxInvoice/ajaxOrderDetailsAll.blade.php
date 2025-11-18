{{-- Print Button --}}
<div class="mb-3">
    <button type="button" class="btn btn-primary" onclick="printOrderDetails('all')">
        <i class="fa fa-print"></i> Print Order Details
    </button>
</div>

<style>
    /* Enhanced table styles for readability */
    #orderDetailsAllTable thead th {
        background: #f1f5f9;
        position: sticky;
        top: 0;
        z-index: 1;
    }
    #orderDetailsAllTable tbody tr:nth-child(even) td {
        background: #fcfdff;
    }
    #orderDetailsAllTable tfoot td {
        font-weight: 700;
        background: #f8fafc;
    }
    #orderDetailsAllTable .category-row td {
        background: #eef2ff;
        font-weight: 700;
    }
    #orderDetailsAllTable .quantity-display {
        font-weight: 600;
    }
    /* Widen Total and Packed columns consistently */
    #orderDetailsAllTable th.col-total,
    #orderDetailsAllTable th.col-packed {
        width: 14%;
        min-width: 140px;
    }
    #orderDetailsAllTable td.col-total,
    #orderDetailsAllTable td.col-packed,
    #orderDetailsAllTable tfoot td.col-total,
    #orderDetailsAllTable tfoot td.col-packed {
        min-width: 140px;
    }
     /* Center all values except the first (product) column */
     #orderDetailsAllTable th,
     #orderDetailsAllTable td { text-align: center; }
     #orderDetailsAllTable td:first-child,
     #orderDetailsAllTable .category-row td { text-align: left; }
     #orderDetailsAllTable th, #orderDetailsAllTable td { border: 1px solid #000; }
</style>

<table class="table table-bordered" id="orderDetailsAllTable">
    <thead>
        <tr>
            <th style="width: 15%">Category/Product</th>
            @foreach($vehicles as $vehicle)
                <th style="width: 10%" class="vehicle-column" data-vehicle-id="{{ $vehicle->id }}">🚚 {{ $vehicle->reg_number }}</th>
            @endforeach
            <th class="col-total" style="width: 14%">Total</th>
            <th class="col-packed" style="width: 14%">Packed</th>
        </tr>
    </thead>
    <tbody>
        @php
            $vehicleTotals = [];
            foreach ($vehicles as $v) { $vehicleTotals[$v->id] = 0; }
            $grandTotal = 0;
        @endphp
        @foreach($categories as $category)
            <tr class="table-primary category-row">
                <td colspan="{{ count($vehicles) + 3 }}">
                    <strong>{{ $category->main_category_name }}</strong>
                </td>
            </tr>
            @foreach($category->subCategories as $product)
                <tr>
                    <td style="padding-left: 30px;">{{ $product->sub_category_name }}</td>
                    @php $rowTotal = 0; @endphp
                    @foreach($vehicles as $vehicle)
                        @php
                            $qty = isset($orderDetails[$product->id][$vehicle->id]) ? (int)$orderDetails[$product->id][$vehicle->id] : 0;
                            $rowTotal += $qty;
                            $vehicleTotals[$vehicle->id] += $qty;
                        @endphp
                        <td>
                            <span class="quantity-display">{{ $qty }}</span>
                            <input type="hidden" name="quantities[{{ $product->id }}][{{ $vehicle->id }}]" value="{{ $qty }}">
                        </td>
                    @endforeach
                    @php $grandTotal += $rowTotal; @endphp
                    <td class="col-total"><strong>{{ $rowTotal }}</strong></td>
                    <td class="col-packed"></td>
                </tr>
            @endforeach
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td><strong>Totals</strong></td>
            @foreach($vehicles as $vehicle)
                <td><strong>{{ $vehicleTotals[$vehicle->id] }}</strong></td>
            @endforeach
            <td class="col-total"><strong>{{ $grandTotal }}</strong></td>
            <td class="col-packed"></td>
        </tr>
    </tfoot>
</table>

<script>
// Structured data for printing without cloning the DOM table
const PRINT_DATA = {
    vehicles: [
@foreach($vehicles as $vehicle)
        { id: {{ $vehicle->id }}, reg_number: @json($vehicle->reg_number) },
@endforeach
    ],
    categories: [
@foreach($categories as $category)
        { name: @json($category->main_category_name), products: [
@foreach($category->subCategories as $product)
            { id: {{ $product->id }}, name: @json($product->sub_category_name) },
@endforeach
        ]},
@endforeach
    ],
    orderDetails: @json($orderDetails)
};

function printOrderDetails(type) {
    // Create a new window
    var printWindow = window.open('', '_blank');

    // Build fresh table HTML from PRINT_DATA
    var headerCols = PRINT_DATA.vehicles.map(function(v){ return `<th class="vehicle-column">${v.reg_number}</th>`; }).join('');
    var tableHeader = `
        <thead>
            <tr>
                <th class="col-product">Varieties</th>
                ${headerCols}
                <th class="col-total">Total</th>
                <th class="col-packed">Packed</th>
            </tr>
        </thead>`;

    var vehicleTotals = {};
    PRINT_DATA.vehicles.forEach(function(v){ vehicleTotals[v.id] = 0; });
    var grandTotal = 0;

    var bodyRows = '';
    PRINT_DATA.categories.forEach(function(cat){
        bodyRows += `<tr class="category-row"><td colspan="${PRINT_DATA.vehicles.length + 3}" style="font-weight:700; background-color: #99A1AF;">${cat.name}</td></tr>`;
        cat.products.forEach(function(p){
            var rowCells = '';
            var rowTotal = 0;
            PRINT_DATA.vehicles.forEach(function(v){
                var qty = 0;
                if (PRINT_DATA.orderDetails && PRINT_DATA.orderDetails[p.id] && PRINT_DATA.orderDetails[p.id][v.id]) {
                    qty = parseInt(PRINT_DATA.orderDetails[p.id][v.id]) || 0;
                }
                rowTotal += qty;
                vehicleTotals[v.id] += qty;
                rowCells += `<td>${qty}</td>`;
            });
            grandTotal += rowTotal;
            bodyRows += `
                <tr>
                    <td style="padding-left: 18px;">${p.name}</td>
                    ${rowCells}
                    <td class="col-total"><strong>${rowTotal}</strong></td>
                    <td class="col-packed"></td>
                </tr>`;
        });
    });

    var footerTotals = PRINT_DATA.vehicles.map(function(v){ return `<td><strong>${vehicleTotals[v.id]}</strong></td>`; }).join('');
    var tableFooter = `
        <tfoot>
            <tr>
                <td><strong>Totals</strong></td>
                ${footerTotals}
                <td class="col-total"><strong>${grandTotal}</strong></td>
                <td class="col-packed"></td>
            </tr>
        </tfoot>`;

    var builtTable = `<table class="table">${tableHeader}<tbody>${bodyRows}</tbody>${tableFooter}</table>`;

    // Create the print page content
    var printContent = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Order Details</title>
            <style>
                /* Base */
                * { box-sizing: border-box; }
                html, body { height: 100%; }
                body { font-family: Arial, sans-serif; margin: 0; }

                /* Page setup to fit one A4 page */
                @page { size: A4 portrait; margin: 10mm; }
                .print-sheet { width: 190mm; margin: 0 auto; }

                /* Header */
                .print-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px; font-size: 12px; }
                .print-header .title { font-weight: 700; font-size: 14px; }
                .print-header .date-line { flex: 1; text-align: right; white-space: nowrap; }

                /* Table styling similar to provided sheet */
                .table { width: 100%; border-collapse: collapse; table-layout: fixed; }
                .table th, .table td { border: 1px solid #000; padding: 3px 4px; font-size: 11px; line-height: 1.1; vertical-align: middle; text-align: center; }
                .table th { background: #99A1AF; }
                /* Category rows: no background for B/W printing; use thicker borders instead */
                .table .category-row td { font-weight: 700; border-top: 2px solid #000; border-bottom: 2px solid #000; }
                .table td:first-child, .table .category-row td { text-align: left; }

                /* Column sizing */
                .col-product { width: 26%; }
                .vehicle-column { width: auto; }
                .col-total { width: 9%; }
                .col-packed { width: 9%; }

                /* Footer notes */
                .print-notes { margin-top: 20px; font-size: 11px; }
                .dotted { border-bottom: 1px dotted #000; height: 20px; margin-top: 2px; }

                /* Ensure everything fits one page */
                .content-area { display: block; }
                .table-wrap { margin-bottom: 0; overflow: visible; }
                .table { page-break-inside: avoid; }

                /* Optional scaling safety for browsers that render slightly larger */
                @media print {
                    body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
                }
            </style>
        </head>
        <body>
            <div class="print-sheet content-area">
                <div class="print-header">
                    <div class="title">Order Details</div>
                    <div class="date-line">Needed Date: ____ / ____ / 20____</div>
                </div>
                <div class="table-wrap">${builtTable}</div>
                <div class="print-notes">
                    <div>Other Notes :</div>
                    <div class="dotted"></div>
                    <div class="dotted"></div>
                    <div class="dotted"></div>
                    <div class="dotted"></div>
                </div>
            </div>

        </body>
        </html>
    `;

    // Write the content to the new window
    printWindow.document.write(printContent);
    printWindow.document.close();

    // Trigger the print dialog automatically with a short delay and close afterwards
    printWindow.onload = function() {
        setTimeout(function(){
            try { printWindow.focus(); } catch (e) {}
            try { printWindow.print(); } finally {
                if ('onafterprint' in printWindow) {
                    printWindow.onafterprint = function() { try { printWindow.close(); } catch (e) {} };
                } else {
                    setTimeout(function() { try { printWindow.close(); } catch (e) {} }, 300);
                }
            }
        }, 50);
    };
}
</script>

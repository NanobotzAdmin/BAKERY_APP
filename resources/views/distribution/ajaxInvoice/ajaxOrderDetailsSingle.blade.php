{{-- Print Button --}}
<div class="mb-3">
    <button type="button" class="btn btn-primary" onclick="printOrderDetails('single')">
        <i class="fa fa-print"></i> Print Order Details
    </button>
</div>

<table class="table table-bordered" id="orderDetailsSingleTable">
    <thead>
        <tr>
            <th style="width: 15%">Category/Product</th>
            <th style="width: 10%">🚚 {{ $selectedVehicle->reg_number }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($categories as $category)
            <tr class="table-primary">
                <td colspan="2">
                    <strong>{{ $category->main_category_name }}</strong>
                </td>
            </tr>
            @foreach($category->subCategories as $product)
                <tr>
                    <td style="padding-left: 30px;">{{ $product->sub_category_name }}</td>
                    <td>
                        <span class="quantity-display">{{ isset($orderDetails[$product->id]) ? $orderDetails[$product->id] : 0 }}</span>
                        <input type="hidden" name="quantities[{{ $product->id }}]"
                            value="{{ isset($orderDetails[$product->id]) ? $orderDetails[$product->id] : 0 }}">
                    </td>
                </tr>
            @endforeach
        @endforeach
    </tbody>
</table>

<script>
function printOrderDetails(type) {
    // Create a new window
    var printWindow = window.open('', '_blank');

    // Get the table HTML
    var tableHtml = document.getElementById('orderDetailsSingleTable').outerHTML;

    // Create the print page content
    var printContent = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Order Details</title>
            <style>
                body { font-family: Arial, sans-serif; }
                .table { width: 100%; border-collapse: collapse; }
                .table th, .table td { border: 1px solid #ddd; padding: 8px; }
                .table-primary { background-color: #f8f9fa; }
                .quantity-display { font-weight: bold; }
                @media print {
                    .no-print { display: none; }
                    .table { page-break-inside: auto; }
                }
            </style>
        </head>
        <body>

            <h2 style="text-align: center; margin-bottom: 20px;">Order Details</h2>
                <div style="text-align: center; margin-top: 20px;" class="no-print">
                <button onclick="window.print()" style="padding: 10px 20px; font-size: 16px; cursor: pointer;">
                    Print
                </button>
            </div>
            ${tableHtml}

        </body>
        </html>
    `;

    // Write the content to the new window
    printWindow.document.write(printContent);
    printWindow.document.close();
}
</script>

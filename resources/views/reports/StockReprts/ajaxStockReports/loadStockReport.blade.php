<?php $id = 0;?>
@foreach ($data as $datas)
<?php $id++?>
<tr>
<td>{{ $id }}</td>
<td>{{ $datas->sub_category_name }}</td>
<td>{{ $datas->batch_code }}</td>
<td>{{ $datas->created_at }}</td>
<td class="sumPrice">{{ $datas->selling_price }}</td>
<td class="sumQty">{{ $datas->available_quantity }}</td>



</tr>


@endforeach
<script>

        $( document ).ready(function() {

            var sumQty = 0;
            $('.sumQty').each(function() {

                var value = $(this).text();
            // add only if the value is number
            if(!isNaN(value) && value.length != 0) {
                sumQty += parseFloat(value);
            }
            });
        $("#sumQtyInput").html(sumQty.toFixed(2));




        var sumPrice = 0;
            $('.sumPrice').each(function() {

                var value = $(this).text();
            // add only if the value is number
            if(!isNaN(value) && value.length != 0) {
                sumPrice += parseFloat(value);
            }
            });
        $("#sumPriceInput").html(sumPrice.toFixed(2));





        });



        </script>

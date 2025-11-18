@php


$privilageId = \DB::table('pm_interfaces')
->select('pm_interfaces.id AS pageId','pm_interface_topic.id AS grupId')
->join('pm_interface_topic', 'pm_interfaces.pm_interface_topic_id', '=', 'pm_interface_topic.id')
->where('pm_interfaces.path','adminPayment')
->first();


@endphp


@extends('layout', ['pageId' => $privilageId->pageId ,'grupId' => $privilageId->grupId ])

@section('content')

<div class="row">
    <div class="col-sm-12">
        <h2 class="font-bold">Payment</h2>
    </div>
</div>


<div class="row">
    <div class="col-sm-12">
        <div class="ibox">
            <div class="ibox-title">
                <h5>Payment</h5>
            </div>
            <div class="ibox-content">

                <div class="form-group row">
                    <div class="form-group col-md-6">
                        <label for="">Select Customer</label>
                        <select class="select2_demo_3 form-control">
                            <option></option>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="">Invoice Type</label>
                        <select class="select2_demo_3 form-control">
                            <option></option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <select id="payment" name="payment" class="select2_demo_3 form-control col-md-4"
                        onchange="paymentType()">
                        <option value="cash" selected="selected">Cash</option>
                        <option value="cheque">Cheque</option>
                    </select>
                </div>

                <div id="cash" class="cash"></div>
                <div id="cheque" class="cheque"></div>

                <div class="form-group row">
                    <div class="form-group col-md-4">
                        <label for="">Amount</label>
                        <input type="text" class="form-control">
                    </div>
                    <div class="form-group col-md-4">
                        <span><br></span>
                        <button type="button" class="btn btn-primary" style="margin-top: 2%">Add Payment</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection


@section('footer')

<script>
    $(".select2_demo_3").select2({
                placeholder: "Select a state",
                allowClear: true
            });




    function paymentType(){   
   
   
   var dropd = document.getElementById("payment");
    
   var text = document.getElementById("cash");
   var text1 = document.getElementById("cheque");
  

   if(dropd.value == "cash"){
      text.innerHTML=' <div class="form-group row"><div class="form-group col-md-4"> <label for="">Paid Amount</label><br><input type="text" class="form-control"> </div> <div class="form-group col-md-4"><span><br></span> <button type="button" style="margin-top:2%;" class="btn btn-primary">Add Payment</button></div> </div>';
      return false;
   }

   else if (dropd.value == "cheque"){
    text.innerHTML=' <div class="form-group row"><div class="form-group col-md-4"> <label for="">Cheque Date</label><br><input type="date" class="form-control"> </div> <div class="form-group col-md-4"> <label for="">Cheque No</label>  <input type="text" class="form-control"></div> </div>';
      return false;
   }


}

    
    
</script>

@endsection
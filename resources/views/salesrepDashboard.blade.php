
@extends('layout', ['pageId' => '0' ,'grupId' => '0' ])

@section('content')

<div class="row">
    <div class="col-sm-12">
            <div class="col-lg-4 mt-5">
                    <a href="">
                        <div class="widget lazur-bg p-xl">
                            <h2>
                                Janet Smith
                            </h2>
                            <ul class="list-unstyled m-t-md">
                                <li>
                                    <span class="fa fa-envelope m-r-xs"></span>
                                    <label>Email:</label>
                                    mike@mail.com
                                </li>
                                <li>
                                    <span class="fa fa-home m-r-xs"></span>
                                    <label>Address:</label>
                                    Street 200, Avenue 10
                                </li>
                                <li>
                                    <span class="fa fa-phone m-r-xs"></span>
                                    <label>Contact:</label>
                                    (+121) 678 3462
                                </li>
                            </ul>
                        </div>
                    </a>
                </div> 
    </div>

</div>

@endsection

@section('footer')

@endsection



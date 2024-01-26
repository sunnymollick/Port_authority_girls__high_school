@extends('backend.layouts.master')
@section('title', 'Add Payment')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <p class="panel-title">Add Payment </p>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <form id='create' action="" enctype="multipart/form-data" method="post"
                                  accept-charset="utf-8">
                                <div id="status"></div>
                                <div class="form-group col-md-5 col-sm-12">
                                    <input type="text" class="form-control" id="barcode" name="barcode"
                                           placeholder="Insert Barcode Number" required>
                                    <span id="error_barcode" class="has-error"></span>
                                </div>
                                <div class="form-group col-md-3 col-sm-12">
                                    <button type="button" class="btn  btn-success form-control"
                                            onclick="paymentDetails()">Filter
                                    </button>
                                </div>
                                <div class="clearfix"></div>
                            </form>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <div id="not_found">
                                <img src="{{asset('assets/images/empty_box.png')}}" width="200px">
                            </div>
                        </div>
                    </div>
                    <div id="payment_content"></div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media screen and (min-width: 768px) {
            #myModal .modal-dialog {
                width: 85%;
                border-radius: 5px;
            }
        }

        #not_found {
            margin-top: 30px;
            z-index: 0;
        }
    </style>


    <script type="text/javascript">

        $(document).ready(function () {
            var div = document.getElementById('payment_content');
            div.style.visibility = 'hidden';
        });


        function paymentDetails() {

            var barcode = $("#barcode").val();

            if (barcode != null) {

                $("#not_found").hide();

                var div = document.getElementById('payment_content');
                div.style.visibility = 'visible';

                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

                $.ajax({
                    url: 'paymentDetails',
                    type: "get",
                    data: {"barcode": barcode, "_token": CSRF_TOKEN},
                    dataType: 'json',
                    beforeSend: function () {
                        $('body').plainOverlay('show');
                    },
                    success: function (data) {
                        $('body').plainOverlay('hide');
                        $("#payment_content").html(data.html);
                    },
                    error: function (result) {
                        $("#payment_content").html("Sorry Cannot Load Data");
                    }
                });
            }
        }
    </script>

@stop
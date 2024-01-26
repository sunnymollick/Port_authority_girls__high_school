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
                                            onclick="includePayment()">Filter
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

                        <div class="col-md-12" id="payment_content">
                            <form id='confirmation' action="" enctype="multipart/form-data" method="post"
                                  accept-charset="utf-8">
                                <div id="status"></div>
                                <div class="table-responsive">
                                    <table id="accounts_payment" class="table table-hover">
                                        <thead>
                                        <tr>
                                            <th>Barcode</th>
                                            <th>Name</th>
                                            <th>Id</th>
                                            <th>Class</th>
                                            <th>Section</th>
                                            <th>Roll</th>
                                            <th>Month</th>
                                            <th>Total Amount</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody id="accounts_payment_data">
                                        </tbody>
                                    </table>
                                </div>
                                <div class="clearfix"></div>
                                <div class="form-group col-md-3 col-sm-12">
                                    <label for=""> Payment Date </label>
                                    <input type="text" class="form-control" id="payment_date" name="payment_date" value=""
                                           placeholder="" required readonly>
                                    <span id="error_payment_date" class="has-error"></span>
                                </div>
                                <div class="clearfix"></div>
                                <div class="form-group col-md-3 col-sm-12">
                                    <button type="submit" class="btn btn-danger button-submit"
                                            data-loading-text="Loading..."><span class="fa fa-check fa-fw"></span>
                                        Payment Confirmation Procced
                                    </button>
                                </div>
                                <div class="clearfix"></div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>

        #not_found {
            margin-top: 30px;
            z-index: 0;
        }

        #accounts_payment th, td {
            font-size: 11px;
        }

        #accounts_payment input[type="text"] {
            border: none
        }

        #accounts_payment th {
            background-color: #78a767;
            color: #f5f5f5;
        }


    </style>


    <script type="text/javascript">

        document.body.classList.add("sidebar-collapse");

        var div = document.getElementById('payment_content');
        div.style.visibility = 'hidden';


        $('#accounts_payment').on('click', 'tr .removeItem', function (e) {
            e.preventDefault();
            $(this).parents('tr').remove();
        });

        function includePayment() {

            var barcode = $("#barcode").val();

            if (barcode) {

                $("#not_found").hide();

                var div = document.getElementById('payment_content');
                div.style.visibility = 'visible';

                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

                $.ajax({
                    url: 'accountsFeeDetails',
                    type: "get",
                    data: {"barcode": barcode, "_token": CSRF_TOKEN},
                    beforeSend: function () {
                        $('body').plainOverlay('show');
                    },
                    success: function (data) {
                        $('body').plainOverlay('hide');
                        $('#accounts_payment_data').prepend(data.html);
                        $("#barcode").val('');
                        var i = 0;

                        $('#accounts_payment tbody tr').each(function () {

                            if (i > 0) {
                                var check_exist = $(this).closest('tr').find("td").find("input").val();

                                if (check_exist == barcode) {
                                    swal("Warning!!", "Entered Barcode data already exist", "warning");
                                    $(this).closest('tr').remove();
                                }
                            }
                            i++;
                        })
                        if (data === 'false') {
                            swal("No Data !!", "No data found", "error");
                        }

                    },
                    error: function (result) {
                        swal("Warning!!", "Insert Barcode", "warning");
                    }
                });
            } else {
                swal("Warning!!", "Insert Barcode", "warning");
            }
        }


        $(document).ready(function () {

            $('#payment_date').datepicker({format: "yyyy-mm-dd"}).on('changeDate', function (e) {
                $(this).datepicker('hide');
            });

            $('#confirmation').validate({// <- attach '.validate()' to your form
                // Rules for form validation
                rules: {
                    payment_date: {
                        required: true
                    }
                },
                // Messages for form validation
                messages: {
                    payment_date: {
                        required: 'Enter  payment date'
                    }
                },
                submitHandler: function (form) {

                    var myData = new FormData($("#confirmation")[0]);
                    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                    myData.append('_token', CSRF_TOKEN);

                    swal({
                        title: "Are you sure?",
                        text: "Before confirmation check everything!!",
                        type: "warning",
                        showCancelButton: true,
                        closeOnConfirm: false,
                        showLoaderOnConfirm: true,
                        confirmButtonClass: "btn-danger",
                        confirmButtonText: "Confirm",
                        cancelButtonText: "Cancel"
                    }, function () {

                        $.ajax({
                            url: 'confirmFeePayment',
                            type: 'POST',
                            data: myData,
                            dataType: 'json',
                            cache: false,
                            processData: false,
                            contentType: false,
                            success: function (data) {

                                if (data.type === 'success') {
                                    swal("Done!", "Payment Successfull", "success");
                                    $('#accounts_payment tbody').empty();

                                } else if (data.type === 'error') {
                                    swal("Error!", data.message, "error");
                                }
                            },
                            error: function (xhr, ajaxOptions, thrownError) {
                                swal("Error!", "Try again", "error");
                            }
                        });
                    });
                }

            });

        });
    </script>

@stop
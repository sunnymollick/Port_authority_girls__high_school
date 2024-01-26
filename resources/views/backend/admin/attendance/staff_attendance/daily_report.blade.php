@extends('backend.layouts.master')
@section('title', 'Daily Attendance Report')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <p class="panel-title">Staff  Daily Attendance Report </p>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <div class="form-group col-md-4 col-sm-12">
                                <input type="text" class="form-control" id="date" name="date"
                                       value="{{date('Y-m-d')}}" placeholder="Select date" required readonly/>
                                <span id="error_date" class="has-error"></span>
                            </div>
                            <div class="form-group  col-xl-2 col-lg-2 col-md-2 col-sm-12 mb-3 mb-lg-0">
                                <button type="button" class="btn  btn-success form-control"
                                        onclick="getDailyattendanceReports()">Filter
                                </button>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <div id="not_found">
                                <img src="{{asset('assets/images/empty_box.png')}}" width="200px">
                            </div>
                            <img id="loader" src="{{asset('assets/images/loadingg.gif')}}" width="20px">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div id="attendance_content"></div>
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

    </style>
    <script>
        document.body.classList.add("sidebar-collapse");

        $(document).ready(function () {
            $('#loader').hide();

            var div = document.getElementById('attendance_content');
            div.style.visibility = 'hidden';
            $('#date').datepicker({format: "yyyy-mm-dd"}).on('changeDate', function (e) {
                $(this).datepicker('hide');
            });
        });

        function getDailyattendanceReports() {

            var atten_date = $("#date").val();

            if (atten_date != '') {

                $("#not_found").hide();
                var div = document.getElementById('attendance_content');
                div.style.visibility = 'visible';

                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

                $.ajax({
                    url: 'staffDailyAttendanceReport',
                    type: "POST",
                    data: {
                        "atten_date": atten_date,
                        "_token": CSRF_TOKEN
                    },
                    dataType: 'json',
                    beforeSend: function () {
                        $('body').plainOverlay('show');
                    },
                    success: function (data) {
                        $('body').plainOverlay('hide');
                        $("#attendance_content").html(data.html);
                    },
                    error: function (result) {
                        $("#attendance_content").html("Sorry Cannot Load Data");
                    }
                });
            }else{
                $('#loader').hide();
                swal("Warning!", "Please Select all field!!", "error");
            }
        }
    </script>

@stop
@extends('backend.layouts.master')
@section('title', 'Monthly Reports')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <p class="panel-title"> Teacher Monthly Attendance Report </p>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <div class="form-group col-md-6 col-sm-12">
                                <select name="month" id="month" class="form-control">
                                    <option value="">Select Month</option>
                                    <option value="1">January</option>
                                    <option value="2">February</option>
                                    <option value="3">March</option>
                                    <option value="4">April</option>
                                    <option value="5">May</option>
                                    <option value="6">June</option>
                                    <option value="7">July</option>
                                    <option value="8">August</option>
                                    <option value="9">September</option>
                                    <option value="10">October</option>
                                    <option value="11">November</option>
                                    <option value="12">December</option>
                                </select>
                            </div>
                            <div class="form-group  col-xl-2 col-lg-2 col-md-2 col-sm-12 mb-3 mb-lg-0">
                                <button type="button" class="btn  btn-success form-control"
                                        onclick="getMonthlyReports()">Filter
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
                    <div class="row">
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

        function getMonthlyReports() {

            var month = $("#month").val();

            if (month != '') {

                $("#not_found").hide();
                var div = document.getElementById('attendance_content');
                div.style.visibility = 'visible';


                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

                $.ajax({
                    url: 'teacherMonthlyAttendanceReport',
                    type: "POST",
                    data: {
                        "month": month,
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
            } else {
                $('#loader').hide();
                swal("Warning!", "Please Select all field!!", "error");
            }
        }
    </script>

    <script type="text/javascript">


        function get_sections(val) {
            $("#section_id").empty();
            $.ajax({
                type: 'GET',
                url: 'getSections/' + val,
                success: function (data) {
                    $("#section_id").html(data);
                },
                error: function (result) {
                    $("#modal_data").html("Sorry Cannot Load Data");
                }
            });
        }


    </script>

@stop
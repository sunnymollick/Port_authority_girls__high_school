@extends('backend.layouts.master')
@section('title', 'Accounts Print Feebook')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <p class="panel-title"> Print Fee Book PDF Files </p>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group col-md-3 col-sm-12">
                                <label for=""> Select Class </label>
                                <select name="class_id" id="class_id" class="form-control"
                                        onchange="get_sections(this.value)" required>
                                    <option value="" selected disabled>Select a class</option>
                                    @foreach($stdclass as $class)
                                        <option value="{{$class->id}}">{{$class->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-4 col-sm-12">
                                <label for=""> Select Section </label>
                                <select class="form-control" name="section_id" id="section_id" required
                                        onchange="getStudents()">
                                    <option value="">Select a section</option>
                                </select>
                            </div>
                            <div class="form-group col-md-5 col-sm-12">
                                <label for=""> Select Student </label>
                                <select class="form-control filter" name="student_id" id="student_id" required>
                                    <option value="">Select a Student</option>
                                </select>
                            </div>
                            <div class="clearfix"></div>
                            <div class="form-group col-md-3 col-sm-12">
                                <label for=""> Month </label>
                                <select name="month" id="month" class="form-control" required>
                                    <option value="" selected disabled="">Select month</option>
                                    <option value="all">All</option>
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
                                <span id="error_month" class="has-error"></span>
                            </div>
                            <div class="clearfix"></div>
                            <div class="form-group  col-xl-2 col-lg-2 col-md-2 col-sm-12 mb-3 mb-lg-0">
                                <button type="button" class="btn  btn-success form-control"
                                        onclick="printFeebook()"> Print Fee Book
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 import_notice">
                            <p> Please Follow The Instructions While Generating PDF Fee Book: </p>
                            <ol>
                                <li>Check Class and Section</li>
                                <li>If you print all student pdf then don't select student</li>
                                <li>If you select all student from a section you have have to wait several minutes. It
                                    need few minutes to generate all student pdf file seperatly.
                                </li>
                                <li>After completed it will appear download files as zipped.</li>
                                <li>You must have to download the file otherwise you have to regenerate it again to
                                    download.
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media screen and (min-width: 768px) {
            #myModal .modal-dialog {
                width: 45%;
                border-radius: 5px;
            }
        }


    </style>

    <script type="text/javascript">

        $(document).ready(function () {
            $('#loader').hide();
            $('.filter').select2();

        });

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

        function getStudents() {

            var class_id = $("#class_id").val();
            var section_id = $("#section_id").val();

            if (class_id != null && section_id != null) {

                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

                $("#student_id").empty();
                $.ajax({
                    type: 'GET',
                    url: 'getAllStudents',
                    data: {"_token": CSRF_TOKEN, "class_id": class_id, "section_id": section_id},
                    success: function (data) {
                        $("#student_id").html(data);
                    },
                    error: function (result) {
                        $("#modal_data").html("Sorry Cannot Load Data");
                    }
                });
            }
        }


        function printFeebook() {

            var class_name = $("#class_id option:selected").text();
            var section_name = $("#section_id option:selected").text();

            var class_id = $("#class_id").val();
            var section_id = $("#section_id").val();
            var student_id = $("#student_id").val();
            var months = $("#month").val();


            if (class_id != null && section_id != null && months != null) {
                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

                $.ajax({
                    type: 'GET',
                    url: 'generateFeePdf',
                    data: {
                        "_token": CSRF_TOKEN,
                        "class_name": class_name,
                        "section_name": section_name,
                        "class_id": class_id,
                        "section_id": section_id,
                        "months": months,
                        "student_id": student_id
                    },
                    beforeSend: function () {
                        $('body').plainOverlay('show');
                    },
                    success: function (data) {

                        if (data.type === 'success') {

                            $('body').plainOverlay('hide');
                            $("#modal_data").empty();
                            $('.modal-title').text('You must download the zipped file');
                            $("#modal_data").html("<a class='btn btn-primary' href='downloadFeeZipped/" + class_name + "/" + section_name + " '> Download </a>");
                            $('#myModal').modal({backdrop: 'static', keyboard: false})

                        } else if (data.type === 'error') {
                            $('body').plainOverlay('hide');
                            $("#modal_data").empty();
                            $('.modal-title').text(data.message);
                            $("#modal_data").html(data.message);
                            $('#myModal').modal('show'); // show bootstrap modal

                        }

                    },
                    error: function (result) {
                        $("#modal_data").html("Sorry Cannot Load Data");
                    }
                });

            } else {
                swal("Warning!!", "Please select class, section and month", "warning");
            }
        }
    </script>

@stop
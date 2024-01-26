@extends('backend.layouts.master')
@section('title', 'Admit Card')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <p class="panel-title"> Admit Card Print </p>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group col-md-2 col-sm-12">
                                <label for=""> Select Class </label>
                                <select name="class_id" id="class_id" class="form-control" required
                                        onchange="get_sections(this.value)">
                                    <option value="" selected disabled>Select a class</option>
                                    @foreach($stdclass as $class)
                                        <option value="{{$class->id}}">{{$class->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-2 col-sm-12">
                                <label for=""> Select Section </label>
                                <select class="form-control" name="section_id" id="section_id" required
                                        onchange="getStudents()">
                                    <option value="">Select a section</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4 col-sm-12">
                                <label for=""> Select a Exam </label>
                                <select name="exam_id" id="exam_id" class="form-control" required>
                                    <option value="" selected disabled>Select a Exam</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4 col-sm-12">
                                <label for=""> Select Student </label>
                                <select class="form-control filter" name="student_id" id="student_id" required>
                                    <option value="">Select a Student</option>
                                </select>
                            </div>
                            <div class="form-group  col-xl-2 col-lg-2 col-md-2 col-sm-12 mb-3 mb-lg-0">
                                <button type="button" id="button-submit" class="btn  btn-success form-control"
                                        onclick="printAdmitCard()">Submit
                                </button>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>

        $(document).ready(function () {
            $('#loader').hide();
            $('.filter').select2();
        });

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

        function printAdmitCard() {

            $("#button-submit").prop('disabled', true); // disable button

            var class_name = $("#class_id option:selected").text();
            var section_name = $("#section_id option:selected").text();

            var exam_id = $("#exam_id").val();
            var class_id = $("#class_id").val();
            var section_id = $("#section_id").val();
            var student_id = $("#student_id").val();


            if (exam_id != null && class_id != null && section_id != null) {
                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

                var base = '/admin/generateAdmitCard';
                var url = base + '?class_id=' + class_id + '&section_id=' + section_id
                    + '&exam_id=' + exam_id + '&student_id=' + student_id
                    + '&class_name=' + class_name + '&section_name=' + section_name;
                window.location.href = url;

            } else {
                swal("Warning!!", "Please select class, section and exam", "warning");
            }
        }
    </script>

    <script type="text/javascript">

        function get_sections(val) {

            get_exams(val);

            $("#section_id").empty();
            $.ajax({
                type: 'GET',
                url: 'getAllSection/' + val,
                success: function (data) {
                    $("#section_id").html(data);
                },
                error: function (result) {
                    $("#modal_data").html("Sorry Cannot Load Data");
                }
            });
        }

        function get_exams(val) {

            $("#exam_id").empty();
            $.ajax({
                type: 'GET',
                url: 'getExams/' + val,
                success: function (data) {
                    $("#exam_id").html(data);
                },
                error: function (result) {
                    $("#modal_data").html("Sorry Cannot Load Data");
                }
            });
        }

    </script>
@stop
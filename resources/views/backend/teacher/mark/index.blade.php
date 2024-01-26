@extends('backend.layouts.teacher_master')
@section('title', 'Marks')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <p class="panel-title"> Manage Marks
                        <a href="{!! route('teacher.importMarks.import') !!}" class="btn btn-danger"
                           style="color: #fff;">Import Marks</a>
                    </p>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <div class="form-group col-md-3 col-sm-12">
                                <select name="exam_id" id="exam_id" class="form-control" required>
                                    <option value="" selected disabled>Select a Exam</option>
                                    @foreach($exams as $exam)
                                        <option value="{{$exam->id}}">{{$exam->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-2 col-sm-12">
                                <select name="class_id" id="class_id" class="form-control" required
                                        onchange="get_sections(this.value)">
                                    <option value="" selected disabled>Select a class</option>
                                    @foreach($stdclass as $class)
                                        <option value="{{$class->id}}">{{$class->name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-md-3 col-sm-12">
                                <select class="form-control" name="section_id" id="section_id"
                                        onchange="get_class_subjects(this.value)" required>
                                    <option value="">Select a section</option>
                                </select>
                            </div>
                            <div class="form-group col-md-2 col-sm-12">
                                <select class="form-control" name="subject_id" id="subject_id" required>
                                    <option value="">Select a subject</option>
                                </select>
                            </div>
                            <div class="form-group  col-xl-2 col-lg-2 col-md-2 col-sm-12 mb-3 mb-lg-0">
                                <button type="button" class="btn  btn-success form-control"
                                        onclick="getMarks()">Filter
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
                        </div>
                    </div>
                    <div class="row">
                        <div id="marks_content"></div>
                    </div>
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
    <script>
        var div = document.getElementById('marks_content');
        div.style.visibility = 'hidden';

        function getMarks() {

            var class_id = $("#class_id").val();
            var section_id = $("#section_id").val();
            var exam_id = $("#exam_id").val();
            var subject_id = $("#subject_id").val();

            if (class_id != null && section_id != null && exam_id != null && subject_id != null) {

                $("#not_found").hide();
                var div = document.getElementById('marks_content');
                div.style.visibility = 'visible';
                $('#manage_all').DataTable().clear();
                $('#manage_all').DataTable().destroy();


                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

                $.ajax({
                    url: 'getMarks',
                    type: "POST",
                    data: {
                        "class_id": class_id,
                        "section_id": section_id,
                        "exam_id": exam_id,
                        "subject_id": subject_id,
                        "_token": CSRF_TOKEN
                    },
                    dataType: 'json',
                    beforeSend: function () {
                        $('body').plainOverlay('show');
                    },
                    success: function (data) {
                        $('body').plainOverlay('hide');
                        $("#marks_content").html(data.html);
                    },
                    error: function (result) {
                        $("#marks_content").html("Sorry Cannot Load Data");
                    }
                });
            } else {
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


        function get_class_subjects(val) {
            $("#subject_id").empty();
            var exam_id = $("#exam_id").val();
            var class_id = $("#class_id").val();
            var section_id = val;
            $.ajax({
                type: 'GET',
                url: 'getSubjects',
                data: {'exam_id': exam_id, 'class_id': class_id, 'section_id': section_id},
                success: function (data) {
                    $("#subject_id").html(data);
                },
                error: function (result) {
                    $("#subject_id").html("Sorry Cannot Load Data");
                }
            });
        }

    </script>
@stop
@extends('backend.layouts.master')
@section('title', 'Junior Marks Sheet')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <p class="panel-title"> Marksheets </p>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <div class="form-group col-md-3 col-sm-12">
                                <select name="class_id" id="class_id" class="form-control" required
                                        onchange="get_sections(this.value)">
                                    <option value="" selected disabled>Select a class</option>
                                    @foreach($stdclass as $class)
                                        <option value="{{$class->id}}">{{$class->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-3 col-sm-12">
                                <select class="form-control" name="section_id" id="section_id" required>
                                    <option value="">Select a section</option>
                                </select>
                            </div>
                            <div class="form-group col-md-3 col-sm-12">
                                <select name="exam_id" id="exam_id" class="form-control" required>
                                    <option value="" selected disabled>Select a Exam</option>
                                </select>
                            </div>
                            <div class="form-group  col-xl-2 col-lg-2 col-md-2 col-sm-12 mb-3 mb-lg-0">
                                <button type="button" class="btn  btn-success form-control"
                                        onclick="jrhalfSummeryResult()">Filter
                                </button>
                            </div>
                            <div class="form-group  col-xl-1 col-lg-1 col-md-1 col-sm-12 mb-3 mb-lg-0">
                                <button type="button" class="btn  btn-success form-control"
                                        onclick="jrhalfSummeryResultGradeSummaryPrint()">Summary
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
                        <div id="tabulations_content"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        @media screen and (min-width: 768px) {
            #myModal .modal-dialog {
                width: 95%;
                border-radius: 5px;
            }
        }

        #not_found {
            margin-top: 30px;
            z-index: 0;
        }

    </style>
    <script>
        document.body.classList.add("sidebar-collapse");

        $('#loader').hide();
        var div = document.getElementById('tabulations_content');
        div.style.visibility = 'hidden';


        function jrhalfSummeryResult() {

            var class_id = $("#class_id").val();
            var section_id = $("#section_id").val();
            var exam_id = $("#exam_id").val();

            var exam_name = $("#exam_id option:selected").text();
            var class_name = $("#class_id option:selected").text();
            var section_name = $("#section_id option:selected").text();

            if (class_id != null && section_id != null && exam_id != null) {

                $("#not_found").hide();
                var div = document.getElementById('tabulations_content');
                div.style.visibility = 'visible';
                $('#manage_all').DataTable().clear();
                $('#manage_all').DataTable().destroy();


                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

                $.ajax({
                    url: 'jrhalfSummeryResult',
                    type: "POST",
                    data: {
                        "class_id": class_id,
                        "section_id": section_id,
                        "exam_id": exam_id,
                        "exam_name": exam_name,
                        "section_name": section_name,
                        "class_name": class_name,
                        "_token": CSRF_TOKEN
                    },
                    dataType: 'json',
                    beforeSend: function () {
                        $('body').plainOverlay('show');
                    },
                    success: function (data) {
                        $('body').plainOverlay('hide');
                        $("#tabulations_content").html(data.html);
                    },
                    error: function (result) {
                        $("#tabulations_content").html("Sorry Cannot Load Data");
                    }
                });
            } else {
                $('#loader').hide();
                swal("Warning!", "Please Select all field!!", "error");
            }
        }
        function jrhalfSummeryResultGradeSummaryPrint() {

            var class_id = $("#class_id").val();
            var section_id = $("#section_id").val();
            var exam_id = $("#exam_id").val();

            var exam_name = $("#exam_id option:selected").text();
            var class_name = $("#class_id option:selected").text();
            var section_name = $("#section_id option:selected").text();

            if (class_id != null && section_id != null && exam_id != null) {

                $("#not_found").hide();
                var div = document.getElementById('tabulations_content');
                div.style.visibility = 'visible';
                $('#manage_all').DataTable().clear();
                $('#manage_all').DataTable().destroy();


                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');



                var base = '{!! route('jrhalfSummeryResultGradeSummaryPrint.access') !!}';
                var url = base + '?class_id=' + class_id + '&section_id=' + section_id +  '&exam_id=' + exam_id + '&class_name=' + class_name + '&section_name=' + section_name
                    + '&exam_name=' + exam_name ;
                window.open(url);
            } else {
                $('#loader').hide();
                swal("Warning!", "Please Select all field!!", "error");
            }
        }
    </script>
    <script type="text/javascript">

        function get_sections(val) {

            get_exams(val)
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
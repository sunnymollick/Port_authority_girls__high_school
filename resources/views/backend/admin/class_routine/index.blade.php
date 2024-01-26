@extends('backend.layouts.master')
@section('title', 'Routines')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <p class="panel-title"> Class Routines
                        @can('routines-create')
                            <button class="btn btn-success" onclick="create()"><i class="glyphicon glyphicon-plus"></i>
                                New Class Routines
                            </button>
                        @endcan
                    </p>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <div class="form-group col-md-5 col-sm-12">
                                <select name="class_id" id="class_id" class="form-control" required
                                        onchange="get_sections(this.value)">
                                    <option value="" selected disabled>Select a class</option>
                                    @foreach($stdclass as $class)
                                        <option value="{{$class->id}}">{{$class->name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-md-5 col-sm-12">
                                <select class="form-control" name="section_id" id="section_id">
                                    <option value="">Select a section</option>
                                </select>
                            </div>
                            <div class="form-group  col-xl-2 col-lg-2 col-md-2 col-sm-12 mb-3 mb-lg-0">
                                <button type="button" class="btn  btn-success form-control"
                                        onclick="getRoutines()">Filter
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
                        <div id="routines_content"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media screen and (min-width: 768px) {
            #myModal .modal-dialog {
                width: 75%;
                border-radius: 5px;
            }
        }

        #not_found {
            margin-top: 30px;
            z-index: 0;
        }

        .dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            z-index: 1000;
            display: none;
            float: left;
            min-width: 127px;
            padding: 5px 0;
            margin: 2px 0 0;
            font-size: 14px;
            text-align: center;
        }

    </style>
    <script>
        document.body.classList.add("sidebar-collapse");
        $(document).ready(function () {
            var div = document.getElementById('routines_content');
            div.style.visibility = 'hidden';
        });

        function getRoutines() {

            var class_id = $("#class_id").val();
            var section_id = $("#section_id").val();
            var class_name = $("#class_id option:selected").text();
            var section_name = $("#section_id option:selected").text();

            if (class_id != null && section_id != null) {

                $("#not_found").hide();
                var div = document.getElementById('routines_content');
                div.style.visibility = 'visible';
                $('#manage_all').DataTable().clear();
                $('#manage_all').DataTable().destroy();


                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

                $.ajax({
                    url: 'getClassroutines',
                    type: "POST",
                    data: {
                        "class_id": class_id,
                        "section_id": section_id,
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
                        $("#routines_content").html(data.html);
                    },
                    error: function (result) {
                        $("#routines_content").html("Sorry Cannot Load Data");
                    }
                });
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


        function create() {

            $("#modal_data").empty();
            $('.modal-title').text('Add New Class Routine'); // Set Title to Bootstrap modal title

            $.ajax({
                type: 'GET',
                url: 'classroutines/create',
                success: function (data) {
                    $("#modal_data").html(data.html);
                    $('#myModal').modal('show'); // show bootstrap modal
                },
                error: function (result) {
                    $("#modal_data").html("Sorry Cannot Load Data");
                }
            });

        }
    </script>
@stop
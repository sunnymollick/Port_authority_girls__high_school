@extends('frontend.layouts.master')
@section('title', ' Class Routine')
@section('content')
    <div class="col-md-12 col-sm-12 m-top-60">
        <div class="section-title text-center">
            <h3>Class Routine</h3>
            <hr/>
        </div>
    </div>

    <div class="col-md-12 col-sm-12 text-center">
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

    <div class="col-md-12 text-center">
        <div id="not_found">
            <img src="{{asset('assets/images/empty_box.png')}}" width="200px">
        </div>
    </div>

    <div class="col-sm-12 col-md-12">
        <div id="routines_content"></div>
    </div>
    <div class="clearfix"></div>

    <script>
        $(document).ready(function () {
            var div = document.getElementById('routines_content');
            div.style.visibility = 'hidden';
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

        function getRoutines() {

            var class_id = $("#class_id").val();
            var section_id = $("#section_id").val();
            var class_name = $("#class_id option:selected").text();
            var section_name = $("#section_id option:selected").text();

            if (class_id != null && section_id != '') {

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
@endsection
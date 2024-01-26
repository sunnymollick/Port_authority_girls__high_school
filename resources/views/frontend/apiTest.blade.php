@extends('frontend.layouts.right_master')
@section('title', ' Students')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <h4>All Students</h4>
            <hr>
        </div>
        <div class="form-group col-md-4 col-sm-12">
            <select name="class_id" id="class_id" class="form-control" required
                    onchange="get_sections(this.value)">
                <option value="" selected disabled>Select a class</option>
                @foreach($stdclass as $class)
                    <option value="{{$class->id}}">{{$class->name}}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group col-md-4 col-sm-12">
            <select class="form-control" name="section_id" id="section_id" required>
                <option value="">Select a section</option>
            </select>
        </div>

        <div class="form-group  col-md-4 col-sm-12">
            <button type="button" class="btn  btn-success form-control"
                    onclick="getStudents()">Filter
            </button>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-12 text-center">
            <div id="not_found"><br/>
                <img src="{{asset('assets/images/empty_box.png')}}" width="200px">
            </div>
        </div>
    </div>
    <br/>
    <div class="row" id="students_content">
    </div>
    <!-- student section end -->

    <script>
        $(document).ready(function () {
            var div = document.getElementById('students_content');
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

        function getStudents() {
            var class_id = $("#class_id").val();
            var section_id = $("#section_id").val();

            if (class_id != null && section_id != null) {
                $("#not_found").hide();
                var div = document.getElementById('students_content');
                div.style.visibility = 'visible';
                $('#manage_all').DataTable().clear();
                $('#manage_all').DataTable().destroy();


                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

                $.ajax({
                    url: 'api/public/getStudent',
                    type: "POST",
                    "data": {"class_id": class_id, "section_id": section_id, "_token": CSRF_TOKEN},
                    dataType: 'json',
                    beforeSend: function () {
                        $('body').plainOverlay('show');
                    },
                    success: function (data) {
                        $('body').plainOverlay('hide');
                        $("#students_content").html(data.data);
                    },
                    error: function (result) {
                        $("#students_content").html("Sorry Cannot Load Data");
                    }
                });
            }
        }
    </script>
@endsection
@extends('frontend.layouts.right_master')
@section('title', ' class Syllabus')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <h4>Class Syllabus</h4>
            <hr/>
        </div>
        <div class="text-center">
            <div class="form-group col-md-4 col-sm-12">
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
            <div class="form-group col-md-3 col-sm-12">
                <button type="button" class="btn  btn-success form-control"
                        onclick="getSyllabus()">Filter
                </button>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-12 text-center">
            <div id="not_found">
                <img src="{{asset('assets/images/empty_box.png')}}" width="200px">
            </div>
        </div>
    </div>
    <br/>
    <div class="row" id="syllabus_content">
        <div class="col-md-12 col-sm-12 table-responsive">
            <table id="manage_all" class="table table-collapse table-bordered table-hover">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Syllabus</th>
                    <th>Subject</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            var div = document.getElementById('syllabus_content');
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

        function getSyllabus() {

            var class_id = $("#class_id").val();
            var section_id = $("#section_id").val();

            if (class_id != null && section_id != null) {

                $("#not_found").hide();
                var div = document.getElementById('syllabus_content');
                div.style.visibility = 'visible';
                $('#manage_all').DataTable().clear();
                $('#manage_all').DataTable().destroy();


                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

                table = $('#manage_all').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        "url": 'getSyllabus',
                        "type": "POST",
                        "data": {"class_id": class_id, "section_id": section_id, "_token": CSRF_TOKEN},
                        "dataType": 'json'
                    },
                    columns: [
                        {data: 'rownum', name: 'rownum'},
                        {data: 'title', name: 'title'},
                        {data: 'file_path', name: 'file_path'},
                        {data: 'subject', name: 'subject'}
                    ],
                    "columnDefs": [
                        {"className": "text-center", "targets": "_all"}
                    ],
                    "autoWidth": false,
                });
                $('.dataTables_filter input[type="search"]').attr('placeholder', 'Type here to search...').css({
                    'width': '220px',
                    'height': '30px'
                });
            }
        }
    </script>

@endsection
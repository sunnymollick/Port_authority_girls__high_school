@extends('backend.layouts.master')
@section('title', 'Students')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <p class="panel-title">Search Students </p>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <div class="form-group col-md-5 col-sm-12">
                                <select name="class_id" id="class_id" class="form-control"
                                        onchange="get_sections(this.value)">
                                    <option value="" selected disabled>Select a class</option>
                                    @foreach($stdclass as $class)
                                        <option value="{{$class->id}}">{{$class->name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-md-5 col-sm-12">
                                <select class="form-control" name="section_id" id="section_id">
                                    <option value="" selected disabled>Select a section</option>
                                </select>
                            </div>
                            <div class="form-group  col-xl-2 col-lg-2 col-md-2 col-sm-12 mb-3 mb-lg-0">
                                <button type="button" class="btn  btn-success form-control"
                                        onclick="getStudents()">Filter
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
                    <div class="row" id="students_content">
                        <div class="col-md-12 col-sm-12 table-responsive">
                            <table id="manage_all" class="table table-collapse table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Student Code</th>
                                    <th>Student Name</th>
                                    <th>Class</th>
                                    <th>Section</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
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
        $(document).ready(function () {
            var div = document.getElementById('students_content');
            div.style.visibility = 'hidden';
        });

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

                table = $('#manage_all').DataTable({
                    processing: true,
                    serverSide: true,
                    pageLength: 25,
                    ajax: {
                        "url": '{!! route('admin.invoiceStudents.students') !!}',
                        "type": "POST",
                        "data": {"class_id": class_id, "section_id": section_id, "_token": CSRF_TOKEN},
                        "dataType": 'json'
                    },
                    columns: [
                        {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                        {data: 'std_code', name: 'std_code'},
                        {data: 'name', name: 'name'},
                        {data: 'class', name: 'class'},
                        {data: 'section', name: 'section'},
                        {data: 'action', name: 'action'}
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


        $("#manage_all").on("click", ".view", function () {

            $("#modal_data").empty();
            $('.modal-title').text('View Student Monthly Fee Book'); // Set Title to Bootstrap modal title

            var id = $(this).attr('id');

            $.ajax({
                url: 'studentFees/' + id,
                type: 'get',
                success: function (data) {
                    $("#modal_data").html(data.html);
                    $('#myModal').modal('show'); // show bootstrap modal
                },
                error: function (result) {
                    $("#modal_data").html("Sorry Cannot Load Data");
                }
            });
        });


    </script>

@stop
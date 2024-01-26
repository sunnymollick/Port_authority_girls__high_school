@extends('backend.layouts.master')
@section('title', 'Students')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <p class="panel-title"> Students
                        @can('student-create')
                            <button class="btn btn-success" onclick="create()"><i class="glyphicon glyphicon-plus"></i>
                                New Student
                            </button>
                        @endcan
                        @can('student-import')
                            <a href="{!! route('admin.importStudents.import') !!}" class="btn btn-danger pull-right"
                               style="color: #fff;">Import Students</a>
                        @endcan
                        <button id="export_excel" class="btn btn-primary" onclick="exportStudent('Excel')"><i
                                class="fa fa-file-excel-o fa-fw"></i>
                            Excel Export
                        </button>
                        <button id="export_pdf" class="btn btn-info" onclick="exportStudent('PDF')"><i
                                class="fa fa-file-pdf-o fa-fw"></i>
                            PDF Export
                        </button>
                    </p>
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
                                    <th width="10px">#</th>
                                    <th>Student ID</th>
                                    <th>Name</th>
                                    <th width="20px">Class</th>
                                    <th width="10px">Section</th>
                                    <th width="10px">Roll</th>
                                    <th width="20px">Status</th>
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
        $("#export_excel").hide();
        $("#export_pdf").hide();

        var div = document.getElementById('students_content');
        div.style.visibility = 'hidden';

        function getStudents() {

            var class_id = $("#class_id").val();
            var section_id = $("#section_id").val();
            var class_name = $("#class_id option:selected").text();
            var section = $("#section_id option:selected").text();

            if (class_id != null && section_id != null) {

                $("#not_found").hide();

                var div = document.getElementById('students_content');
                div.style.visibility = 'visible';
                $('#manage_all').DataTable().clear();
                $('#manage_all').DataTable().destroy();


                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

                table = $('#manage_all').DataTable({
                    dom: "<'row'<'col-sm-4'l><'col-sm-8'f>>" +
                    "<'row'<'col-sm-12'>B>" + //
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-4'i><'col-sm-8'p>>",
                    processing: true,
                    serverSide: true,
                    pageLength: 50,
                    ajax: {
                        "url": '{!! route('admin.allStudents.students') !!}',
                        "type": "POST",
                        "data": {
                            "class_id": class_id, "section_id": section_id,
                            "class_name": class_name, "section": section,
                            "_token": CSRF_TOKEN
                        },
                        "dataType": 'json'
                    },
                    "initComplete": function (settings, json) {
                        // $("#export_excel").show();
                        //  $("#export_pdf").show();
                    },
                    columns: [
                        {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                       // {data: 'file_path', name: 'file_path'},
                        {data: 'std_code', name: 'std_code'},
                        {data: 'name', name: 'name'},
                        {data: 'class_name', name: 'class_name'},
                        {data: 'section_name', name: 'section_name'},
                        {data: 'roll', name: 'roll'},
                        {data: 'status', name: 'status'},
                        {data: 'action', name: 'action'}
                    ],
                    buttons: [
                        {
                            extend: 'excelHtml5',
                            text: '<i class="fa fa-table"> EXCEL </i>',
                            titleAttr: 'Excel',
                            exportOptions: {
                                columns: ':visible:not(.not-exported)'
                            }
                        },
                        {
                            extend: 'pdfHtml5',
                            title: "{!! $app_settings->name  !!} \n Students Information \n",
                            text: '<i class="fa fa-file-pdf-o"> PDF</i>',
                            titleAttr: 'PDF',
                            filename: 'Students',
                            exportOptions: {
                                columns: ':visible'
                            },
                            customize: function (doc) {
                                doc.content[1].table.headerRows = 0
                                doc.pageMargins = [100, 10, 20, 10];
                                doc.defaultStyle.fontSize = 9;
                                doc.styles.tableHeader.fontSize = 9;
                                doc.styles.title.fontSize = 14;
                                // Remove spaces around page title
                                doc.content[0].text = doc.content[0].text.trim();
                                doc['footer'] = (function (page, pages) {
                                    return {
                                        columns: [
                                            '{{ $app_settings->name }}',
                                            {
                                                // This is the right column
                                                alignment: 'right',
                                                text: ['page ', {text: page.toString()}, ' of ', {text: pages.toString()}]
                                            }
                                        ],
                                        margin: [10, 0]
                                    }
                                });
                            }
                        },
                        {
                            extend: 'print',
                            title: "<div class='text-center'>{!! $app_settings->name  !!} <br/> Students Information </div>",
                            text: '<i class="fa fa-print"> PRINT </i>',
                            titleAttr: 'Print',
                            exportOptions: {
                                columns: ':visible'
                            }

                        }, {
                            extend: 'colvis',
                            text: '<i class="fa fa-eye-slash"> Column Visibility </i>',
                            titleAttr: 'Visibility'
                        }

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

        function exportStudent(val) {

            var class_id = $("#class_id").val();
            var section_id = $("#section_id").val();

            if (class_id != null && section_id != null) {
                if (val == 'Excel') {
                    var url = 'exportStudentExcel/' + class_id + '/' + section_id;
                    window.location.href = url;
                } else {
                    var url = 'exportStudentPdf/' + class_id + '/' + section_id;
                    window.location.href = url;
                }

            }
        }

        function get_sections(val) {
            if (val != 'all') {
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
        }


        function create() {

            $("#modal_data").empty();
            $('.modal-title').text('Add New Student'); // Set Title to Bootstrap modal title

            $.ajax({
                type: 'GET',
                url: 'students/create',
                success: function (data) {
                    $("#modal_data").html(data.html);
                    $('#myModal').modal('show'); // show bootstrap modal
                },
                error: function (result) {
                    $("#modal_data").html("Sorry Cannot Load Data");
                }
            });

        }


        $("#manage_all").on("click", ".edit", function () {

            $("#modal_data").empty();
            $('.modal-title').text('Edit Students'); // Set Title to Bootstrap modal title

            var id = $(this).attr('id');

            $.ajax({
                url: 'students/' + id + '/edit',
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

        $("#manage_all").on("click", ".view", function () {

            $("#modal_data").empty();
            $('.modal-title').text('View Students'); // Set Title to Bootstrap modal title

            var id = $(this).attr('id');

            $.ajax({
                url: 'students/' + id,
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

        $("#manage_all").on("click", ".password", function () {

            $("#modal_data").empty();
            $('.modal-title').text('Change Password'); // Set Title to Bootstrap modal title

            var id = $(this).attr('id');

            $.ajax({
                url: 'std_change_password/' + id,
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
    <script type="text/javascript">

        $(document).ready(function () {

            $("#manage_all").on("click", ".delete", function () {
                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                var id = $(this).attr('id');
                swal({
                    title: "Are you sure",
                    text: "Deleted data cannot be recovered!!",
                    type: "warning",
                    showCancelButton: true,
                    closeOnConfirm: false,
                    showLoaderOnConfirm: true,
                    confirmButtonStudents: "btn-danger",
                    confirmButtonText: "Delete",
                    cancelButtonText: "Cancel"
                }, function () {
                    $.ajax({
                        url: 'students/' + id,
                        data: {"_token": CSRF_TOKEN},
                        type: 'DELETE',
                        dataType: 'json',
                        success: function (data) {

                            if (data.type === 'success') {

                                swal("Done!", "Successfully Deleted", "success");
                                reload_table();

                            } else if (data.type === 'danger') {

                                swal("Error deleting!", "Try again", "error");

                            }
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                            swal("Error deleting!", "Try again", "error");
                        }
                    });
                });
            });
        });

    </script>
@stop
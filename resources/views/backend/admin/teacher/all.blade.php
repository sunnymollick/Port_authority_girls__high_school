@extends('backend.layouts.master')
@section('title', ' All Teachers')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <p class="panel-title"> All Teachers
                        @can('teacher-create')
                            <button class="btn btn-success" onclick="create()"><i class="glyphicon glyphicon-plus"></i>
                                New Teacher
                            </button>
                        @endcan
                        @can('parent-import')
                            <a href="{!! route('admin.importTeachers.import') !!}" class="btn btn-danger pull-right"
                               style="color: #fff;">Import Teachers</a>
                        @endcan
                    </p>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12 col-sm-12 table-responsive">
                            <table id="manage_all" class="table table-collapse table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Photo</th>
                                    <th>Name</th>
                                    <th>Teacher ID</th>
                                    <th>Qualification</th>
                                    <th>Designation</th>
                                    <th>Phone</th>
                                    <th>Status</th>
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
                width: 80%;
            }
        }
    </style>
    <script>
        $(function () {
            table = $('#manage_all').DataTable({
                dom: "<'row'<'col-sm-4'l><'col-sm-8'f>>" +
                "<'row'<'col-sm-12'>B>" + //
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-4'i><'col-sm-8'p>>",
                processing: true,
                serverSide: true,
                pageLength: 50,
                ajax: '{!! route('admin.allTeachers.teachers') !!}',
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'file_path', name: 'file_path'},
                    {data: 'name', name: 'name'},
                    {data: 'teacher_code', name: 'teacher_code'},
                    {data: 'qualification', name: 'qualification'},
                    {data: 'designation', name: 'designation'},
                    {data: 'phone', name: 'phone'},
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
                        title: "{!! $app_settings->name  !!} \n Teachers Information \n",
                        text: '<i class="fa fa-file-pdf-o"> PDF</i>',
                        titleAttr: 'PDF',
                        filename: 'Teachers',
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
                        title: "<div class='text-center'>{!! $app_settings->name  !!} <br/> Teachers Information </div>",
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
        });
    </script>
    <script type="text/javascript">

        function reload_table() {
            table.ajax.reload(null, false); //reload datatable ajax
        }


        function create() {

            $("#modal_data").empty();
            $('.modal-title').text('Add New Teacher'); // Set Title to Bootstrap modal title

            $.ajax({
                type: 'GET',
                url: 'teachers/create',
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
            $('.modal-title').text('Edit Teacher'); // Set Title to Bootstrap modal title

            var id = $(this).attr('id');

            $.ajax({
                url: 'teachers/' + id + '/edit',
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
            $('.modal-title').text('View Teacher'); // Set Title to Bootstrap modal title

            var id = $(this).attr('id');

            $.ajax({
                url: 'teachers/' + id,
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
                url: 'teacher_change_password/' + id,
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
                    title: "Are you sure?",
                    text: "Deleted data cannot be recovered!!",
                    type: "warning",
                    showCancelButton: true,
                    closeOnConfirm: false,
                    showLoaderOnConfirm: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Delete",
                    cancelButtonText: "Cancel"
                }, function () {
                    $.ajax({
                        url: 'teachers/' + id,
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

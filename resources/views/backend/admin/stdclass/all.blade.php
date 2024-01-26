@extends('backend.layouts.master')
@section('title', ' All Classes')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <p class="panel-title"> All Classes
                        @can('stdclass-create')
                            <button class="btn btn-success" onclick="create()"><i class="glyphicon glyphicon-plus"></i>
                                New Class
                            </button>
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
                                    <th>Class Name</th>
                                    <th>In Digit</th>
                                    <th>Sections</th>
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
                width: 60%;
                border-radius: 5px;
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
                ajax: '{!! route('admin.allClasses.classes') !!}',
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'name', name: 'name'},
                    {data: 'in_digit', name: 'in_digit'},
                    {data: 'section', name: 'section'},
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
                        title: "{!! $app_settings->name  !!} \n Class Information \n Session : {!! $app_settings->running_year  !!}",
                        text: '<i class="fa fa-file-pdf-o"> PDF</i>',
                        titleAttr: 'PDF',
                        filename: 'Class Information',
                        exportOptions: {
                            columns: ':visible'
                        },
                        customize: function (doc) {
                            doc.content[1].table.headerRows = 0
                            doc.pageMargins = [100, 10, 20, 10];
                            doc.defaultStyle.fontSize = 9;
                            doc.styles.tableHeader.fontSize = 9;
                            doc.styles.title.fontSize = 10;
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
                        title: "<div class='text-center'>{!! $app_settings->name  !!} <br/> Class Information <br/> Session : {!! $app_settings->running_year  !!} </div>",
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
            $('.modal-title').text('Add New Class'); // Set Title to Bootstrap modal title

            $.ajax({
                type: 'GET',
                url: 'stdclasses/create',
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
            $('.modal-title').text('Edit Class'); // Set Title to Bootstrap modal title

            var id = $(this).attr('id');

            $.ajax({
                url: 'stdclasses/' + id + '/edit',
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
            $('.modal-title').text('View Class'); // Set Title to Bootstrap modal title

            var id = $(this).attr('id');

            $.ajax({
                url: 'stdclasses/' + id,
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
                    text: "Deleting of a class can delete all related data like sections, students, subjects, books etc.",
                    type: "warning",
                    showCancelButton: true,
                    closeOnConfirm: false,
                    showLoaderOnConfirm: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Delete",
                    cancelButtonText: "Cancel"
                }, function () {
                    $.ajax({
                        url: 'stdclasses/' + id,
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

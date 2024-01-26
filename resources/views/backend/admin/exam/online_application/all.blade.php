@extends('backend.layouts.master')
@section('title', ' All Applications')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <p class="panel-title"> All Applications
                        @can('online-application-create')
                            <button class="btn btn-success" onclick="create()"><i class="glyphicon glyphicon-plus"></i>
                                New Application
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
                                    <th>Applicant Name</th>
                                    <th>ID</th>
                                    <th>Father's Name</th>
                                    <th>Mother's Name</th>
                                    <th>Father's Occupation</th>
                                    <th>Father Yearly Income</th>
                                    <th>Admited Class</th>
                                    <th>Admited Section</th>
                                    <th>Mobile</th>
                                    <th>status</th>
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
                width: 95%;
                border-radius: 5px;
            }
        }

    </style>

    <script>
        document.body.classList.add("sidebar-collapse");
        $(document).ready(function () {

            table = $('#manage_all').DataTable({
                dom: "<'row'<'col-sm-4'l><'col-sm-8'f>>" +
                "<'row'<'col-sm-12'>B>" + //
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-4'i><'col-sm-8'p>>",

                "lengthMenu": [[-1], ["All"]],

                "autoWidth": false,
                "scrollX": true,
                ajax: '/admin/allApplications',
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'applicant_name_en', name: 'applicant_name_en'},
                    {data: 'applicant_id', name: 'applicant_id'},
                    {data: 'father_name_en', name: 'father_name_en'},
                    {data: 'mother_name_en', name: 'mother_name_en'},
                    {data: 'father_occupation', name: 'father_occupation'},
                    {data: 'yearly_income', name: 'yearly_income'},
                    {data: 'class_name', name: 'class_name'},
                    {data: 'section_name', name: 'section_name'},
                    {data: 'mobile', name: 'mobile'},
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
                        title: "Online Admission Application \n",
                        text: '<i class="fa fa-file-pdf-o"> PDF</i>',
                        titleAttr: 'PDF',
                        filename: 'online_admission_application',
                        exportOptions: {
                            columns: ':visible'
                        },
                        customize: function (doc) {
                            doc.content[1].table.headerRows = 0
                            doc.pageMargins = [20, 10, 20, 10];
                            doc.defaultStyle.fontSize = 6;
                            doc.styles.tableHeader.fontSize = 6;
                            doc.styles.title.fontSize = 8;
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
                        title: "<div class='text-center'></div>",
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

                "oSelectorOpts": {filter: 'applied', order: "current"},
                language: {
                    buttons: {},

                    "emptyTable": "<strong style='color:#ff0000'> Sorry!!! No Records have found </strong>",
                    "search": "",
                    "paginate": {
                        "next": "Next",
                        "previous": "Previous"
                    },

                    "zeroRecords": ""
                }
            });


            $('.dataTables_filter input[type="search"]').attr('placeholder', 'Type here to search...').css({'width': '220px'});

            $('[data-toggle="tooltip"]').tooltip();

        });
    </script>
    <script type="text/javascript">

        function reload_table() {
            table.ajax.reload(null, false); //reload datatable ajax
        }


        function create() {

            $("#modal_data").empty();
            $('.modal-title').text('Add New Application'); // Set Title to Bootstrap modal title

            $.ajax({
                type: 'GET',
                url: 'admissionApplication/create',
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
            $('.modal-title').text('Edit Application'); // Set Title to Bootstrap modal title

            var id = $(this).attr('id');

            $.ajax({
                url: 'admissionApplication/' + id + '/edit',
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
            $('.modal-title').text('View Application'); // Set Title to Bootstrap modal title

            var id = $(this).attr('id');

            $.ajax({
                url: 'admissionApplication/' + id,
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
                        url: 'admissionApplication/' + id,
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

@extends('backend.layouts.master')
@section('title', 'Accounts Fee Management')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <p class="panel-title">Exceptional Student Accounts Fee Management </p>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <form id='create' action="" enctype="multipart/form-data" method="post" accept-charset="utf-8">
                            <div id="status"></div>
                            <div class="form-group col-md-3 col-sm-12">
                                <label for=""> Select Class </label>
                                <select name="class_id" id="class_id" class="form-control"
                                        onchange="get_sections(this.value)" required>
                                    <option value="" selected disabled>Select a class</option>
                                    @foreach($stdclass as $class)
                                        <option value="{{$class->id}}">{{$class->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-4 col-sm-12">
                                <label for=""> Select Section </label>
                                <select class="form-control" name="section_id" id="section_id" required
                                        onchange="getStudents()">
                                    <option value="">Select a section</option>
                                </select>
                            </div>
                            <div class="form-group col-md-5 col-sm-12">
                                <label for=""> Select Student </label>
                                <select class="form-control filter" name="student_id" id="student_id" required>
                                    <option value="">Select a Student</option>
                                </select>
                            </div>
                            <div class="clearfix"></div>
                            <div class="form-group col-md-3 col-sm-12">
                                <label for=""> Month </label>
                                <select name="month" id="month" class="form-control" required>
                                    <option value="">Select month</option>
                                    <option value="1">January</option>
                                    <option value="2">February</option>
                                    <option value="3">March</option>
                                    <option value="4">April</option>
                                    <option value="5">May</option>
                                    <option value="6">June</option>
                                    <option value="7">July</option>
                                    <option value="8">August</option>
                                    <option value="9">September</option>
                                    <option value="10">October</option>
                                    <option value="11">November</option>
                                    <option value="12">December</option>
                                </select>
                                <span id="error_month" class="has-error"></span>
                            </div>
                            <div class="form-group col-md-6 col-sm-12">
                                <label for=""> Select Accounts Head </label>
                                <select name="accounts_head_id" id="accounts_head_id" class="form-control filter"
                                        required>
                                    <option value="" selected disabled>Select Accounts Head</option>
                                    @foreach($accountsHead as $value)
                                        <option value="{{$value->id}}">{{$value->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-3">
                                <div class="form-group">
                                    <label for="amount"> Amount </label>
                                    <input type="text" class="form-control" id="amount" name="amount" value=""
                                           placeholder="" required>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="form-group col-md-12">
                                <button type="button" onclick="getExceptionalStudentsFees()"
                                        class="btn btn-default"><span
                                        class="fa fa-search fa-fw"></span> Filter
                                </button>
                                <button type="submit" class="btn btn-success button-submit"
                                        data-loading-text="Loading..."><span class="fa fa-save fa-fw"></span> Save
                                </button>
                            </div>
                        </form>
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
                            <table id="ex_manage_all" class="table table-collapse table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Student ID</th>
                                    <th>Student Name</th>
                                    <th>Class</th>
                                    <th>Section</th>
                                    <th>Accounts Head</th>
                                    <th>Amount</th>
                                    <th>Month</th>
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
                width: 70%;
                border-radius: 5px;
            }
        }

        #ex_manage_all th, td {
            font-size: 11px;
        }
    </style>
    <script>
        document.body.classList.add("sidebar-collapse");
        var div = document.getElementById('students_content');
        div.style.visibility = 'hidden';

        $(document).ready(function () {
            $('#loader').hide();
            $('.filter').select2();

        });


        function create() {

            $("#modal_data").empty();
            $('.modal-title').text('Add New Exceptional Student Roles'); // Set Title to Bootstrap modal title

            $.ajax({
                type: 'GET',
                url: 'accountsExceptionalStudent/create',
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

    <script>

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

                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

                $("#student_id").empty();
                $.ajax({
                    type: 'GET',
                    url: 'getAllStudents',
                    data: {"_token": CSRF_TOKEN, "class_id": class_id, "section_id": section_id},
                    success: function (data) {
                        $("#student_id").html(data);
                    },
                    error: function (result) {
                        $("#modal_data").html("Sorry Cannot Load Data");
                    }
                });
            }
        }


        function reload_table() {
            table.ajax.reload(null, false); //reload datatable ajax
        }


        function getExceptionalStudentsFees() {

            var class_id = $("#class_id").val();
            var section_id = $("#section_id").val();
            var student_id = $("#student_id").val();
            var month = $("#month").val();

            if (class_id != null && section_id != null && student_id != null) {

                $("#not_found").hide();
                var div = document.getElementById('students_content');
                div.style.visibility = 'visible';
                $('#ex_manage_all').DataTable().clear();
                $('#ex_manage_all').DataTable().destroy();


                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

                table = $('#ex_manage_all').DataTable({
                    dom: "<'row'<'col-sm-4'l><'col-sm-8'f>>" +
                    "<'row'<'col-sm-12'>B>" + //
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-4'i><'col-sm-8'p>>",
                    processing: true,
                    serverSide: true,
                    pageLength: 25,
                    ajax: {
                        "url": 'getExceptionalStudentfeeDetails',
                        "type": "GET",
                        "data": {
                            "class_id": class_id,
                            "section_id": section_id,
                            "student_id": student_id,
                            "month": month,
                            "_token": CSRF_TOKEN
                        },
                        "dataType": 'json'
                    },
                    columns: [
                        {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                        {data: 'std_code', name: 'std_code'},
                        {data: 'name', name: 'name'},
                        {data: 'class_name', name: 'class_name'},
                        {data: 'section', name: 'section'},
                        {data: 'accounts_head', name: 'accounts_head'},
                        {data: 'amount', name: 'amount'},
                        {data: 'month', name: 'month'},
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
                            title: "{!! $app_settings->name  !!} \n Exceptional Student Fee Information \n Session : {!! $app_settings->running_year  !!}",
                            text: '<i class="fa fa-file-pdf-o"> PDF</i>',
                            titleAttr: 'PDF',
                            filename: 'Exceptional Student Fee Information',
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
                            title: "<div class='text-center'>{!! $app_settings->name  !!} <br/> Exceptional Student Fee Information <br/> Session : {!! $app_settings->running_year  !!} </div>",
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

        $(document).ready(function () {


            $('.filter').select2();

            $('#loader').hide();

            $('#create').validate({// <- attach '.validate()' to your form
                // Rules for form validation
                rules: {
                    name: {
                        required: true
                    }
                },
                // Messages for form validation
                messages: {
                    name: {
                        required: 'Enter name'
                    }
                },
                submitHandler: function (form) {


                    var myData = new FormData($("#create")[0]);
                    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                    myData.append('_token', CSRF_TOKEN);

                    $.ajax({
                        url: 'accountsExceptionalStudent',
                        type: 'POST',
                        data: myData,
                        dataType: 'json',
                        cache: false,
                        processData: false,
                        contentType: false,
                        beforeSend: function () {
                            $('body').plainOverlay('show');
                            $("#submit").prop('disabled', true); // disable button
                        },
                        success: function (data) {

                            if (data.type === 'success') {
                                $('body').plainOverlay('hide');
                                $("#status").html(data.message);
                                notify_view(data.type, data.message);
                                getExceptionalStudentsFees()
                                $("#submit").prop('disabled', false); // disable button

                            } else if (data.type === 'error') {
                                $('body').plainOverlay('hide');
                                if (data.errors) {
                                    $.each(data.errors, function (key, val) {
                                        $('#error_' + key).html(val);
                                    });
                                }
                                $("#status").html(data.message);
                                $("#submit").prop('disabled', false); // disable button

                            }

                        }
                    });


                }
                // <- end 'submitHandler' callback
            });                    // <- end '.validate()'

        });

        $(document).ready(function () {
            $("#ex_manage_all").on("click", ".delete", function () {
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
                        url: 'accountsExceptionalStudent/' + id,
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
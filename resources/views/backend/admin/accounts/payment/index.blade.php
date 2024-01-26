@extends('backend.layouts.master')
@section('title', 'Students')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <p class="panel-title">Payment History
                        <button id="export_excel" class="btn btn-primary" onclick="exportPaymentHistory('Excel')"><i
                                class="fa fa-file-excel-o fa-fw"></i>
                            Excel Export
                        </button>
                        <button id="export_pdf" class="btn btn-info" onclick="exportPaymentHistory('PDF')"><i
                                class="fa fa-file-pdf-o fa-fw"></i>
                            PDF Export
                        </button>
                    </p>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <div class="form-group col-md-3 col-sm-12">
                                <select name="class_id" id="class_id" class="form-control"
                                        onchange="get_sections(this.value)">
                                    <option value="" selected disabled>Select class</option>
                                    @foreach($stdclass as $class)
                                        <option value="{{$class->id}}">{{$class->name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-md-3 col-sm-12">
                                <select class="form-control" name="section_id" id="section_id">
                                    <option value="" selected disabled>Select section</option>
                                </select>
                            </div>
                            <div class="form-group col-md-3 col-sm-12">
                                <select name="month" id="month" class="form-control" required>
                                    <option value="" selected disabled>Select month</option>
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
                            <div class="form-group col-md-3 col-sm-12">
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
                                    <th>Student Id</th>
                                    <th>Name</th>
                                    <th>Class</th>
                                    <th>Section</th>
                                    <th>Title</th>
                                    <th>Month</th>
                                    <th>Amount</th>
                                    <th>Paid</th>
                                    <th>Due</th>
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
                width: 85%;
                border-radius: 5px;
            }
        }

        #not_found {
            margin-top: 30px;
            z-index: 0;
        }

        table th, td {
            font-size: 12px;
        }


    </style>
    <script>

        $("#export_excel").hide();
        $("#export_pdf").hide();

        document.body.classList.add("sidebar-collapse");

        $(document).ready(function () {
            var div = document.getElementById('students_content');
            div.style.visibility = 'hidden';

        });

        function getStudents() {

            var class_id = $("#class_id").val();
            var section_id = $("#section_id").val();
            var month = $("#month").val();

            if (class_id != null && section_id != null && month != null) {

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
                        "url": '{!! route('admin.paymentStudents.students') !!}',
                        "type": "POST",
                        "data": {"class_id": class_id, "section_id": section_id, "month": month, "_token": CSRF_TOKEN},
                        "dataType": 'json'
                    },
                    "initComplete": function (settings, json) {
                        $("#export_excel").show();
                        $("#export_pdf").show();
                    },
                    columns: [
                        {data: 'rownum', name: 'rownum'},
                        {data: 'std_code', name: 'std_code'},
                        {data: 'name', name: 'name'},
                        {data: 'class', name: 'class'},
                        {data: 'section', name: 'section'},
                        {data: 'title', name: 'title'},
                        {data: 'month', name: 'month'},
                        {data: 'amount', name: 'amount'},
                        {data: 'paid', name: 'paid'},
                        {data: 'due', name: 'due'},
                        {data: 'status', name: 'status'},
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
            }else {
                swal("Warning!", "Please Select all field!!", "error");
            }
        }
    </script>

    <script type="text/javascript">

        function exportPaymentHistory(val) {

            var class_id = $("#class_id").val();
            var section_id = $("#section_id").val();
            var month = $("#month").val();

            if (class_id != null && section_id != null && month != null) {

                if (val == 'Excel') {

                    var url = 'exportExcelPaymentHistory/' + class_id + '/' + section_id + '/' + month;
                    window.location.href = url;

                } else {
                    var url = 'exportPdfPaymentHistory/' + class_id + '/' + section_id + '/' + month;
                    window.location.href = url;
                }

            } else {
                alert()
            }
        }

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
            $('.modal-title').text('View Fee Details'); // Set Title to Bootstrap modal title

            var id = $(this).attr('id');

            $.ajax({
                url: 'invoiceDetails/' + id,
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
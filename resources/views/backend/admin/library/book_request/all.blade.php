@extends('backend.layouts.master')
@section('title', 'All Books Request')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <p class="panel-title"> All Book Requests
                        @can('book-issue-create')
                            <button class="btn btn-success" onclick="create()"><i class="glyphicon glyphicon-plus"></i>
                                New Book Request
                            </button>
                        @endcan
                    </p>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12 col-md-offset-2">
                            <div class="form-group col-md-5 col-sm-12">
                                <select name="reports_term" id="reports_term" class="form-control" required>
                                    <option value="all_issued" selected>All Issued Reports</option>
                                    <option value="all_returned">All Returned Reports</option>
                                    <option value="last_week">Last 7 Days Issued Reports</option>
                                    <option value="this_month">This Month All Reports</option>
                                    <option value="last_month">Last Month All Reports</option>
                                </select>
                            </div>
                            <div class="form-group  col-xl-2 col-lg-2 col-md-2 col-sm-12 mb-3 mb-lg-0">
                                <button type="button" class="btn  btn-success form-control"
                                        onclick="reload_table()">Filter
                                </button>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-sm-12 table-responsive">
                            <table id="manage_all" class="table table-collapse table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Book Name</th>
                                    <th>Student Code</th>
                                    <th>Issued Date</th>
                                    <th>Issue End Date</th>
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
        $(document).ready(function () {
            reload_table();
        });


        function reload_table() {
            $('#manage_all').DataTable().clear();
            $('#manage_all').DataTable().destroy();

            var term = $("#reports_term").val();
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

            table = $('#manage_all').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 25,
                ajax: {
                    "url": '{!! route('admin.allRequests.bookrequests') !!}',
                    "type": "POST",
                    "data": {"reports_term": term, "_token": CSRF_TOKEN},
                    "dataType": 'json'
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'book_id', name: 'book_id'},
                    {data: 'student_code', name: 'student_code'},
                    {data: 'issue_start_date', name: 'issue_start_date'},
                    {data: 'issue_end_date', name: 'issue_end_date'},
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
        }
    </script>
    <script type="text/javascript">

        function create() {

            $("#modal_data").empty();
            $('.modal-title').text('Issue New Book'); // Set Title to Bootstrap modal title

            $.ajax({
                type: 'GET',
                url: 'bookrequests/create',
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
            $('.modal-title').text('Edit Issued Books'); // Set Title to Bootstrap modal title

            var id = $(this).attr('id');

            $.ajax({
                url: 'bookrequests/' + id + '/edit',
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
            $('.modal-title').text('View Books'); // Set Title to Bootstrap modal title

            var id = $(this).attr('id');

            $.ajax({
                url: 'bookrequests/' + id,
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
                        url: 'bookrequests/' + id,
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

@extends('backend.layouts.master')
@section('title', 'Bank Accounts Lists')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <p class="panel-title"> Bank Accounts Lists
                        @can('accounts-head-create')
                            <button class="btn btn-success" onclick="create()"><i class="glyphicon glyphicon-plus"></i>
                                New Bank Accounts
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
                                    <th>Account Name</th>
                                    <th>Account Number</th>
                                    <th>Bank Name</th>
                                    <th>Branch</th>
                                    <th>Contact Person</th>
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
                width: 90%;
                border-radius: 5px;
            }
        }
    </style>
    <script>
        $(function () {
            table = $('#manage_all').DataTable({
                processing: true,
                serverSide: true,
                ajax: 'allBankAccounts',
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'account_name', name: 'account_name'},
                    {data: 'account_number', name: 'account_number'},
                    {data: 'bank_name', name: 'bank_name'},
                    {data: 'branch_name', name: 'branch_name'},
                    {data: 'contact_person', name: 'contact_person'},
                    {data: 'phone', name: 'phone'},
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
        });
    </script>
    <script type="text/javascript">

        function reload_table() {
            table.ajax.reload(null, false); //reload datatable ajax
        }


        function create() {

            $("#modal_data").empty();
            $('.modal-title').text('Add New Accounts'); // Set Title to Bootstrap modal title

            $.ajax({
                type: 'GET',
                url: 'bankAccounts/create',
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
            $('.modal-title').text('Edit Accounts'); // Set Title to Bootstrap modal title

            var id = $(this).attr('id');

            $.ajax({
                url: 'bankAccounts/' + id + '/edit',
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
                        url: 'bankAccounts/' + id,
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

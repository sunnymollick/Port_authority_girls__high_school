@extends('backend.layouts.student_master')
@section('title', 'Print Fee Books')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <p class="panel-title"> All Fee Books </p>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12 col-sm-12 table-responsive">
                            <table id="manage_all" class="table table-collapse table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Student Id</th>
                                    <th>Name</th>
                                    <th>Class</th>
                                    <th>Section</th>
                                    <th>Roll</th>
                                    <th>Month</th>
                                    <th>Barcode</th>
                                    <th>Amount</th>
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
                width: 98%;
                border-radius: 5px;
            }
        }
    </style>
    <script>
        $(function () {
            table = $('#manage_all').DataTable({
                processing: true,
                serverSide: true,
                ajax: 'allFeeBooks',
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'std_code', name: 'std_code'},
                    {data: 'name', name: 'name'},
                    {data: 'class_name', name: 'class_name'},
                    {data: 'section_name', name: 'section_name'},
                    {data: 'roll', name: 'roll'},
                    {data: 'month', name: 'month'},
                    {data: 'barcode', name: 'barcode'},
                    {data: 'total_amount', name: 'total_amount'},
                    {data: 'action', name: 'action'},
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

        $("#manage_all").on("click", ".view", function () {

            $("#modal_data").empty();
            $('.modal-title').text('Monthly Fee Book'); // Set Title to Bootstrap modal title

            var id = $(this).attr('id');

            var url = 'printFeeBook/' + id;
            window.location.href = url;
            
        });

    </script>
@stop

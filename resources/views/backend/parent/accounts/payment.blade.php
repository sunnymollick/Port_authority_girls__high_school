@extends('backend.layouts.parent_master')
@section('title', 'Payment Reports')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <p class="panel-title"> My Payment Reports </p>
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
                                    <th>Status</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(function () {
            table = $('#manage_all').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{!! route('parent.paymentDetails') !!}',
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'std_code', name: 'std_code'},
                    {data: 'name', name: 'name'},
                    {data: 'class_name', name: 'class_name'},
                    {data: 'section_name', name: 'section_name'},
                    {data: 'roll', name: 'roll'},
                    {data: 'month', name: 'month'},
                    {data: 'barcode', name: 'barcode'},
                    {data: 'amount', name: 'amount'},
                    {data: 'status', name: 'status'},
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
@stop

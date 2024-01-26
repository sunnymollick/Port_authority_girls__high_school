@extends('frontend.layouts.right_master')
@section('title', 'Download Digital Contents')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <h4> Download Digital Content</h4>
            <hr>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 col-sm-12 table-responsive">
            <table id="manage_all" class="table table-collapse table-bordered table-hover">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Download</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
    <script>
        $(function () {
            table = $('#manage_all').DataTable({
                processing: true,
                serverSide: true,
                ajax: 'allDownloads',
                columns: [
                    {data: 'rownum', name: 'rownum'},
                    {data: 'title', name: 'title'},
                    {data: 'file_path', name: 'file_path'}
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





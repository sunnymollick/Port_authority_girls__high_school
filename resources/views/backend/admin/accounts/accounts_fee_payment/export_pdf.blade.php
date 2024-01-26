<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <style type="text/css">

        #heading {
            width: 35%;
            text-align: center;
            font-size: 13px;
            margin-left: 32%;
            background: #edefec;
            padding: 5px;

        }

        #heading h4 {
            line-height: 2px;
        }

        table th, td {
            text-align: left;
            border: 1px solid #727b83;
            font-size: 12px;
            padding: 5px;
        }

        .divTableHeading {
            background-color: #eee;
            display: table-header-group;
            font-weight: bold;
        }

        #table_1 {
            border-collapse: collapse;
            width: 100%;
        }

    </style>
</head>
<body style="page-break-after: auto">
@php
    $i = 1;
@endphp
@if(count($data) > 0)
    <div id="marks_pdf">
        <div id="heading">
            <h4> {{ $app_settings->name }}</h4>
            <h4> {{ 'Student Fee Reports' }}</h4>
            <h4> Class : {{ $data[0]->class_name }} </h4>
            <h4> Section : {{ $data[0]->section_name }} </h4>
            <h4> {{ $monthName . ', ' . $app_settings->running_year }} </h4>
        </div>&nbsp;
        <table id="table_1">
            <thead class="divTableHeading">
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
            <tbody>
            @foreach($data as $row)
                <tr>
                    <td>{{ $i++ }}</td>
                    <td>{{ $row->std_code }}</td>
                    <td>{{ $row->name }}</td>
                    <td>{{ $row->class_name }}</td>
                    <td>{{ $row->section_name }}</td>
                    <td>{{ $row->roll }}</td>
                    <td>{{ $monthName }}</td>
                    <td>{{$row->barcode }}</td>
                    <td>{{$row->amount }}</td>
                    <td>{{$row->status}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endif
</body>
</html>
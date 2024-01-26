<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <style type="text/css">

        .heading p {
            text-align: center;
            font-size: 11px;
            margin-left: -80px;
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
<div id="student_pdf">
    <table id="table_1">
        <thead class="divTableHeading">
        <tr>
            <th>Student ID</th>
            <th>Student Name</th>
            <th>Class</th>
            <th>Section</th>
            <th>Roll</th>
        </tr>
        </thead>
        <tbody>
        @foreach($students as $value)
            <tr>
                <td>{{$value->std_code}}</td>
                <td>{{$value->name}}</td>
                <td>{{$value->class_name}}</td>
                <td>{{$value->section}}</td>
                <td>{{$value->roll}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
</body>
</html>
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
@if(count($data) > 0)
    <div id="marks_pdf">
        <div id="heading">
            <h4> {{ $data[0]->exam_name }}</h4>
            <h4> Class : {{ $data[0]->class_name }} </h4>
            <h4> Section : {{ $data[0]->section }} </h4>
            <h4> Subject : {{ $data[0]->sub_name }} </h4>
        </div>
        &nbsp;
        @if(!$session_year->special)
            <table id="table_1">
            <thead class="divTableHeading">
            <tr>
                <th class="serial">#</th>
                <th class="std_id">Student Code</th>
                <th class="std_name">Name</th>
                <th class="text-center">Roll</th>
                <th class="marks">Theory Marks</th>
                <th class="marks">MCQ Marks</th>
                <th class="marks">Practical Marks</th>
                <th class="marks">CT Marks</th>
            </tr>
            </thead>
            <tbody>
            @php $no = 1 @endphp
            @foreach($data as $row)
                <tr>
                    <td>{{ $no++ }}</td>
                    <td>{{ $row->std_code }}</td>
                    <td>{{ $row->std_name }}</td>
                    <td class="text-center">{{ $row->std_roll }}</td>
                    <td>{{$row->theory_marks}}</td>
                    <td>{{$row->mcq_marks }}</td>
                    <td>{{$row->practical_marks}}</td>
                    <td>{{$row->ct_marks}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        @else
            <table id="table_1">
                    <thead class="divTableHeading">
                    <tr>
                        <th class="serial">#</th>
                        <th class="std_id">Student Code</th>
                        <th class="std_name">Name</th>
                        <th class="text-center">Roll</th>
                        <th class="marks">Written Marks</th>
                        <th class="marks">Assignment Marks</th>
                        <th class="marks">Other Marks</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php $no = 1 @endphp
                    @foreach($data as $row)
                        <tr>
                            <td>{{ $no++ }}</td>
                            <td>{{ $row->std_code }}</td>
                            <td>{{ $row->std_name }}</td>
                            <td class="text-center">{{ $row->std_roll }}</td>
                            <td>{{$row->written_marks}}</td>
                            <td>{{$row->assignment_marks }}</td>
                            <td>{{$row->other_marks}}</td>
                        </tr>
                    @endforeach
                    </tbody>
            </table>
        @endif
    </div>
@endif
</body>
</html>
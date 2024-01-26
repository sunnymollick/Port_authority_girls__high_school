<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>
<body style="page-break-after: auto">
@if(!empty($data))
    <div class="" id="marksheet">
        <div id="header_details">
            <div id="col_1">
                <p style="text-align: left"><strong>Student Name : {{ $data['student_name'] }}</strong> <br/>
                    <strong>Class : </strong> {{ $data['class_name'] }} <br/>
                    <strong>Roll : </strong> {{ $data['std_roll'] }}
                </p>
            </div>
            <div id="col_2">
                <p style=" text-align: center">
                    <strong>{{ $app_settings ? $app_settings->name : '' }}</strong>
                    <br/>
                    <strong> Exam : {{ $data['exam_name'] }} </strong>
                </p>
            </div>
            <div id="col_3">
                <p style="text-align: right">
                    <strong>Student ID : </strong>{{ $data['student_code']}}<br/>
                    <strong> Session : </strong>{{ $data['year'] }}
                </p>
            </div>
        </div>
        &nbsp;
        @php
            $total_marks = 0;
            $cgpa_status = 1;
            $total_gpa = 0;
            $total_cgpa = 0;
            $optional_sub_marks = 0;
            $total_subjects = 0;

            $total_subjects = count($data['result']);

        foreach($data['result'] as $row) {

          $total_marks+= $row->obtainedMark;

           if ($row->result_status === 'F') {
               $cgpa_status = 0;
            }

            if ($cgpa_status != 0) {
              $total_cgpa = round($total_cgpa + $row->CGPA, 2);
            }

            if ($row->subject_id == $row->optional_subject) {

                $total_subjects = count($data['result']) -1; // Optional subject not count on average point so less
                $total_cgpa = $total_cgpa - $row->CGPA;

                if ($row->CGPA > 2.00) {
                    $optional_sub_marks = $row->CGPA - 2.00;
                    $total_cgpa = $total_cgpa + $optional_sub_marks;
                }
            }
        }

        $cgpa = sprintf('%0.2f', $total_cgpa / $total_subjects);
        $cgpa = $cgpa>5? '5.00':$cgpa;

        if ($cgpa_status != 0) {

            if ($cgpa >= 5) {
                $gpa = "A+";
            } else if ($cgpa >= 4 and $cgpa <= 4.99) {
                $gpa = "A";
            } else if ($cgpa >= 3.50 and $cgpa <= 3.99) {
                $gpa = "A-";
            } else if ($cgpa >= 3 and $cgpa <= 3.49) {
                $gpa = "B";
            } else if ($cgpa >= 2 and $cgpa <= 2.99) {
                $gpa = "C";
            } else if ($cgpa >= 1 and $cgpa <= 1.99) {
                $gpa = "D";
            } else {
                $gpa = "<strong class='points'> Failed </strong>";
            }
        } else {
            $gpa = "<strong class='points'> Failed </strong>";
        }
        $total_numbers = "<strong class='points'>" . $total_marks . "</strong>";
        $cgpa = $cgpa_status == '1' ? "<strong class='points'>" . $cgpa . "</strong>" : "<strong class='points'> Failed </strong>";

        @endphp


        <div id="marks_table">
            <table id="table_1">
                <thead class="divTableHeading">
                <tr>
                    <th style="text-align: left"> Subject</th>
                    <th> Subject Marks</th>
                    @if ($data['has_ct'] != 0)
                        <th> {{'CT (' . $data['has_ct'] . '%)' }}</th>
                    @endif
                    <th> Theory</th>
                    <th> MCQ</th>
                    <th> Practical</th>
                    {{--<th> Total</th>--}}
                    @if ($data['has_ct'] != 0)
                        <th> {{'Main (' . $data['mmp'] . '%)' }}</th>
                    @endif
                    <th> Marks Obtained</th>
                    <th> Highest</th>
                    <th> GP</th>
                    <th> Grade</th>
                </tr>
                </thead>
                <tbody>
                @foreach($data['result'] as $row)
                    @php
                        if($row->subject_code ==101 || $row->subject_code ==102 || $row->subject_code ==107 || $row->subject_code ==108 || $row->subject_code ==133 || $row->subject_code ==156  ){

                        $gp = "";
                        $grade = "";
                        }else{

                        $gp = $row->CGPA;
                        $grade = $row->grade;
                        }

                    @endphp
                    <tr>
                        <td class="sub_name">{{ $row->subject }} {{$row->subject_id == $row->optional_subject ? ' ( Optional Subject ) ' : '' }}</td>
                        <td>{{ $row->subject_marks}}</td>
                        @if ($data['has_ct'] != 0)
                            <td>{{ $row->ctPMarks}}</td>
                        @endif
                        <td>{{ $row->theory_marks }}</td>
                        <td>{{ $row->mcq_marks ? $row->mcq_marks: '-' }}</td>
                        <td>{{ $row->practical_marks ? $row->practical_marks: '-' }}</td>
                        {{--<td>{{  $row->total_marks }}</td>--}}
                        @if ($data['has_ct'] != 0)
                            <td>{{ $row->mainPMarks }}</td>
                        @endif
                        <td>{{  $row->obtainedMark }}</td>
                        <td>{{ $row->highest_marks }}</td>
                        <td>{{ $gp }}</td>
                        <td>{{ $grade }}</td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot style="display: none">
                <tr>
                    <td>Total Marks</td>
                    @if ($data['has_ct'] != 0)
                        <td></td>
                    @endif
                    <td></td>
                    <td></td>
                    <td></td>
                    @if ($data['has_ct'] != 0)
                        <td></td>
                    @endif
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                </tfoot>
            </table>
        </div>
        <br/>
        <div id="sum_1">
            <table id="table_2">
                <tbody>
                <tr>
                    <td width="120px;">Total Marks</td>
                    <td width="10px;">:</td>
                    <td width="80px;">{!! $total_numbers !!} </td>
                    <td width="300px;" rowspan="9">Comments :</td>
                </tr>
                <tr>
                    <td>Grade Point Average</td>
                    <td>:</td>
                    <td> </td>
                    {{--{!! $cgpa !!}--}}
                </tr>
                <tr>
                    <td>Grade</td>
                    <td>:</td>
                    <td> </td>
                    {{--{!! $gpa !!}--}}
                </tr>
                <tr>
                    <td>Total Students</td>
                    <td>:</td>
                    <td>{{$data['total_std']}}</td>
                </tr>
                <tr>
                    <td>Total Working Days</td>
                    <td>:</td>
                    <td>{{$data['total_wd']}}</td>
                </tr>
                <tr>
                    <td>Total Attendance</td>
                    <td>:</td>
                    <td>{{$data['total_atd']}}</td>
                </tr>
                {{--<tr>--}}
                    {{--<td>Position</td>--}}
                    {{--<td>:</td>--}}
                    {{--<td>{{$data['position']}}</td>--}}
                {{--</tr>--}}
                <tr>
                    <td>Parent's Signature</td>
                    <td>:</td>
                    <td></td>
                </tr>
                <tr>
                    <td>Teacher's Signature</td>
                    <td>:</td>
                    <td></td>
                </tr>
                <tr>
                    <td>Head Teacher's Signature</td>
                    <td>:</td>
                    <td>
                        <img style="border: none;" src="{{ asset('assets/images/head_master.png') }}"
                             class="img-responsive img-thumbnail"
                             width="50px"/>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div id="sum_2">
            <table id="table_3">
                <thead class="divTableHeading">
                <tr>
                    <th> Score</th>
                    <th> Grade</th>
                    <th> CGPA</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>80-100</td>
                    <td>A+</td>
                    <td>5.00</td>
                </tr>
                <tr>
                    <td>70-79</td>
                    <td>A</td>
                    <td>4.00</td>
                </tr>
                <tr>
                    <td>60-69</td>
                    <td>A-</td>
                    <td>3.50</td>
                </tr>
                <tr>
                    <td>50-59</td>
                    <td>B</td>
                    <td>3.00</td>
                </tr>
                <tr>
                    <td>40-49</td>
                    <td>C</td>
                    <td>2.00</td>
                </tr>
                <tr>
                    <td>33-39</td>
                    <td>D</td>
                    <td>1.00</td>
                </tr>
                <tr>
                    <td>0-32</td>
                    <td>F</td>
                    <td>0.00</td>
                </tr>
                </tbody>
            </table>

        </div>
    </div>
@else
    <div class="row">
        <div class="col-md-12 text-center">
            <div id="not_found">
                <img src="{{asset('assets/images/empty_box.png')}}" width="200px">
            </div>
            <h2>No data found of this requirement</h2>
        </div>
    </div>
@endif
<style>

    .points {
        color: #0b2e13;
    }

    #header_details {
        width: 100%;
        display: block;
    }

    #header_details p {
        font-size: 13px;
    }

    #marks_table {
        position: relative;
        width: 100%;
        display: block;
        margin-top: 100px;
    }

    .divTableHeading {
        background-color: #eee;
        display: table-header-group;
        font-weight: bold;
    }

    #col_1, #col_2, #col_3 {
        width: 33.3%;
        float: left;
        position: relative;
    }

    #manage_all_result td, th {
        text-align: center;
    }


    input {
        border: 1px solid #f6f6f6;
    }

    .heading p {
        text-align: center;
        font-size: 13px;
        margin-left: -80px;
    }

    .footer p {
        text-align: center;
        font-size: 13px;
    }

    table th, td {
        text-align: left;
        border: 1px solid #727b83;
        font-size: 12px;
        padding: 1px;
        overflow: hidden;
        line-height: 20px;
    }

    #table_1 {
        border-collapse: collapse;
        width: 100%;
        line-height: 20px;
    }

    #table_2 {
        border-collapse: collapse;
        width: 68%;
        float: left;
        text-align: left;
        line-height: 20px;
    }

    #table_3 {
        width: 30%;
        float: right;
        line-height: 20px;
    }


</style>
</body>

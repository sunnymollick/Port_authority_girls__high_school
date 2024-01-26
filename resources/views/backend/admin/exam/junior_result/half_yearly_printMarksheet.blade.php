<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>
<body style="page-break-after: auto">
@if(!empty($data))
    <div class="" id="marksheet">
        <div id="header_details">
            <div class="header_image_title">


                <div style="margin-left: 80px;">
                    <h1 style="font-size:30px; color: #1f2698; font-weight: bolder; padding:0; margin:0; text-align: center;">&nbsp;<br>&nbsp;{{ $app_settings ? $app_settings->name : '' }}</h1>
                    <p style="text-align: center; padding:0; margin:0; ">
                        <strong style="font-size:26px; color: green;">{{ $data['exam_name'] }} </strong>
                    </p>
                </div>
                <div style="position: absolute; top:15px; left: 250px; ">
                        <img style="border: none;" src="{{ asset('assets/images/port_school_logo.png') }}"
                    class="img-responsive img-thumbnail"
                    width="80px"/>
                </div>
                {{-- <strong style="padding-left:115px; padding-top:100px; font-size:16px;"> Exam : {{ $data['exam_name'] }} </strong> --}}
            </div>
            {{-- <div class="header_image">
                <img style="border: none;" src="{{ asset('assets/images/port_school_logo.png') }}"
                class="img-responsive img-thumbnail"
                width="70px"/>

                <strong style="font-size:20px;">&nbsp;<br>&nbsp;{{ $app_settings ? $app_settings->name : '' }}</strong> <br>

            </div>
            <div style="margin-left: 80px; margin-top: -5px; ">
                <p style="text-align: left">
                    <strong style="font-size:17px;"> Exam : {{ $data['exam_name'] }} </strong>
                </p>
            </div> --}}
            <div id="col_1">
                <p style="text-align: left; font-size: 16px;"><strong><span style="color: #923495;">Name of the Student :</span> {{ $data['student_name'] }}</strong> <br/>
                    <strong><span style="color: #923495;">Class :</span> </strong> {{ $data['class_name'] }} &nbsp;&nbsp; <strong><span style="color: #923495;">Section :</span> </strong> {{ $data['section_name'] }}<br/>
                    <strong><span style="color: #923495;">Roll :</span> </strong> {{ $data['std_roll'] }}
                </p>
            </div>
            <div id="col_2">
                <p style=" text-align: center">
                    &nbsp;
                </p>
            </div>
            <div id="col_3">
                <p style="text-align: right; font-size: 16px;">
                    <strong><span style="color: #923495;">Student ID :</span> </strong>{{ $data['student_code']}}<br/>
                    {{-- <strong> Session : </strong>{{ $data['year'] }} --}}
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

           if ($row->grade === 'F') {
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
        $cgpa = $cgpa_status == '1' ? "<strong class='points'>" . $cgpa . "</strong>" : "<strong class='points'> 0.00 </strong>";

        @endphp

        @if(!$session_year->special)
            <div id="marks_table">
                <table id="table_1">
                    <thead class="divTableHeading">
                    <tr>
                        <th > Subject</th>
                        <th> Total Marks of Subject</th>
                        <th> CA</th>
                        <th> Creative & MCQ</th>
                        <th> Total Marks</th>
                        <th> Highest</th>
                        <th> Grade</th>
                        <th> Grade Point</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data['result'] as $row)
                        <tr>
                            <td class="sub_name" >{{ $row->subject }} {{$row->subject_id == $row->optional_subject ? ' ( Optional Subject ) ' : '' }}</td>
                            <td>{{ $row->subject_marks}}</td>
                            <td>{{ $row->ctPMarks}}</td>
                            <td>{{ $row->theoryPMarks }}</td>
                            <td>{{ $row->obtainedMark }}</td>
                            <td>{{ $row->highest_marks }}</td>
                            <td>{{ $row->grade }}</td>
                            <td>{{ $row->CGPA }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot style="display: none">
                    <tr>
                        <td>Total Marks</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <div id="marks_table">
                <table id="table_1">
                    <thead class="divTableHeading">
                    <tr>
                        <th> Subject</th>
                        <th> Subject Marks</th>
                        <th> Written</th>
                            <th> Assignment</th>
                            <th> Cleanliness / Tree Plantation</th>
                            <th> Marks Obtained</th>
                            <th> Highest</th>
                            <th> Grade Point</th>
                            <th> Grade</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data['result'] as $row)
                        <tr>
                            <td class="sub_name" style=" text-align: center; font-weight: bold;">{{ $row->subject }} {{$row->subject_id == $row->optional_subject ? ' ( Optional Subject ) ' : '' }}</td>
                            <td style=" text-align: center;">{{ $row->subject_marks}}</td>
                            <td style=" text-align: center;">{{ $row->writtenMarks}}</td>
                                <td style=" text-align: center;">{{ $row->assignmentMarks }}</td>
                                <td style=" text-align: center;">{{ $row->otherMarks }}</td>
                                <td style=" text-align: center;">{{ $row->obtainedMark }}</td>
                                <td style=" text-align: center;">{{ $row->highest_marks }}</td>
                                <td style=" text-align: center;">{{ $row->CGPA }}</td>
                                <td style=" text-align: center;">{{ $row->grade }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot style="display: none">
                    <tr>
                        <td>Total Marks</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        @endif
        <br/>
        <div id="sum_1">
            <table id="table_2">
                <tbody>
                <tr>
                    <td width="120px;" style="font-weight: bold; text-align: right;">Total Marks Obtained</td>
                    <td width="10px;">:</td>
                    <td width="80px;">{!! $total_numbers !!} </td>
                    <td width="150px;" rowspan="8" style="vertical-align: top; text-align: center; color: blue; font-weight: bold;">Comments :</td>
                </tr>
                <tr>
                    <td style="font-weight: bold; text-align: right;">GPA</td>
                    <td>:</td>
                    <td>{!! $cgpa !!}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold; text-align: right;">Grade</td>
                    <td>:</td>
                    <td>{!! $gpa !!}</td>
                </tr>
                <tr>
                    <td class="sub_name" style="font-weight: bold; text-align: right;">Class Rank</td>
                    <td>:</td>
                    <td>{{ $data['class_rank'] }}</td>
                </tr>
                <tr>
                    <td class="sub_name" style="font-weight: bold; text-align: right;">Section Rank</td>
                    <td>:</td>
                    <td>{{ $data['rank'] }}</td>
                </tr>

                <tr>
                    <td style="font-weight: bold; text-align: right;">Total Students</td>
                    <td>:</td>
                    <td>{{$data['total_std']}}</td>
                </tr>

                <!--<tr>-->
                <!--    <td style="font-weight: bold; text-align: right;">Parent's Signature</td>-->
                <!--    <td>:</td>-->
                <!--    <td></td>-->
                <!--</tr>-->
                <tr>
                    <td style="font-weight: bold; text-align: right;">Class Teacher's Signature</td>
                    <td>:</td>
                    <td>
                        <img style="border: none; text-align: right; width: 50px; max-height: 35px;" src="{{ asset('assets/images/'.$data['class_name'].'_'.$data['section_name'].'.jpeg') }}"
                             class="img-responsive img-thumbnail"
                             width="50px"/>
                    </td>
                </tr>
                <tr>
                    <td style="font-weight: bold; text-align: right;">Head Teacher's Signature</td>
                    <td>:</td>
                    <td>
                        <img style="border: none; text-align: right;" src="{{ asset('assets/images/new_head_teacher_signature.jpeg') }}"
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
                    <th> GP</th>
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
                {{-- <tr>
                    <td>33-39</td>
                    <td>D</td>
                    <td>1.00</td>
                </tr> --}}
                <tr>
                    <td>0-39</td>
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
{{-- <style>

    .points {
        color: #0b2e13;
    }

    #header_details {
        width: 100%;
        display: block;
    }

    #header_details p {
        font-size: 15px;
    }

    #marks_table {
        position: relative;
        width: 100%;
        display: block;
        margin-top: 60px;
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
        /* text-align: left; */
        border: 1px solid #727b83;
        font-size: 13px;
        padding: 1px;
        overflow: hidden;
        line-height: 18px;
    }

    #table_1 {
        text-align: center;
        border-collapse: collapse;
        width: 100%;
        line-height: 18px;
    }

    #table_2 {
        border-collapse: collapse;
        width: 60%;
        float: left;
        text-align: left;
        line-height: 18px;
    }

    #table_3 {
        width: 38%;
        float: right;
        line-height: 18px;
        text-align: center;
    }

    #table_2 td:nth-child(1) {
        text-align: right;
    }

    #table_1 td:nth-child(1) {
        font-weight: bold;
    }
    .header_image img {
        float: left;
        margin-left: 280px;

    }

    .header_image h1 {
        position: relative;
        top: 18px;
        left: 10px;
    }


</style> --}}
<style>

    .points {
        color: #0b2e13;
    }

    #header_details {
        width: 100%;
        display: block;
    }

    #header_details p {
        font-size: 15px;
    }

    #marks_table {
        position: relative;
        width: 100%;
        display: block;
        margin-top: 60px;
    }

    .divTableHeading {
        background-color: #eee;
        display: table-header-group;
        font-weight: bold;
    }

    #col_1, #col_3 {
        width: 40%;
        float: left;
        position: relative;
    }
    #col_2 {
        width: 19.9%;
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
        /* text-align: left; */
        border: 1px solid #727b83;
        font-size: 13px;
        padding: 1px;
        overflow: hidden;
        line-height: 18px;
    }

    #table_1 {
        text-align: center;
        border-collapse: collapse;
        width: 100%;
        line-height: 18px;
    }

    #table_2 {
        border-collapse: collapse;
        width: 100%;
        /* float: left; */
        text-align: left;
        line-height: 18px;
    }

    #table_3 {
        width: 100%;
        /* float: right; */
        line-height: 18px;
        text-align: center;
    }



    #table_2 td:nth-child(1) {
        text-align: right;
    }

    #table_1 td:nth-child(1) {
        font-weight: bold;
        color: green;
    }
    #table_1 td:nth-child(2) {
        font-weight: bold;
    }

    .header_image img {
        /* float: left;
        margin-left: 280px; */

    }

    .header_image h1 {
        /* position: relative;
        top: 18px;
        left: 10px; */
    }
    .header_image_title{
        position: relative;
    }

    #sum_1{
        float: left;
        width: 60%;
    }

    #sum_2{
        margin-left: 65%;
        width: 35%;
    }
    body {
        border: 7px solid #ab8731;
        padding: 0 15px;
        box-sizing: border-box;
        border-radius: 5px;
    }
    #table_1 th{
        background: #a0ddfc;
        color: blue;
    }
    #table_3 th{
        background: #d3e6af;
        color: green;
    }
    #table_3 td{
        color: green;
    }
    #table_2 td:nth-child(1) {
        text-align: right;
        color: maroon;
    }





</style>
</body>

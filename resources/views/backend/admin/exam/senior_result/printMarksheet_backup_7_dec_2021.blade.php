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
        &nbsp;@php
            $total_marks = 0;
            $cgpa_status = 1;
            $total_gpa = 0;
            $total_cgpa = 0;
            $optional_sub_marks = 0;
            $total_subjects = 0;

             $rowspan = 0;

            $bangla_marks = 0;
            $combined_bangla = 0;
            $combined_bangla_marks = 0;
            $bangla_both_theory = 0;
            $bangla_both_mcq = 0;

            $eng_marks = 0;
            $combined_eng = 0;
            $combined_eng_marks = 0;

            $total_subjects = count($data['result']);

        foreach($data['result'] as $row) {

          $total_marks+= $row->obtainedMark;

           if ($row->result_status === 'F') {
               $cgpa_status = 0;
            }

            if ($cgpa_status != 0) {
              $total_cgpa = round($total_cgpa + $row->CGPA, 2);
            }

            //Special Check
                if(!$session_year->special){
                // Bangla combined
                if ($row->subject_code == 101 || $row->subject_code == 102) {
                    $combined_bangla = $combined_bangla + 1;
                    $combined_bangla_marks = $combined_bangla_marks + $row->obtainedMark;
                    $total_cgpa = $total_cgpa - $row->CGPA;
                    $bangla_both_theory = $bangla_both_theory + $row->theoryPMarks;
                    $bangla_both_mcq = $bangla_both_mcq + $row->mcq_marks;
                }



                // English combined
                if ($row->subject_code == 107 || $row->subject_code == 108) {
                    $combined_eng = $combined_eng + 1;
                    $combined_eng_marks = $combined_eng_marks + $row->obtainedMark;
                    $total_cgpa = $total_cgpa - $row->CGPA;
                }


                // carrer and physical education combined
                if ($row->subject_code == 156 || $row->subject_code == 133) {
                    $total_subjects = $total_subjects -1; //  subject not count on average point so less
                    $total_cgpa = $total_cgpa - $row->CGPA;
                }


                // Optional subject calculation
                if ($row->subject_id == $row->optional_subject) {

                    $total_subjects = $total_subjects -1; // Optional subject not count on average point so less
                    $total_cgpa = $total_cgpa - $row->CGPA;

                    if ($row->CGPA > 2.00) {
                        $optional_sub_marks = $row->CGPA - 2.00;
                        $total_cgpa = $total_cgpa + $optional_sub_marks;
                    }
                }
            }
        }

        //dd($combined_bangla_marks);

        if($combined_bangla == 2){
                $total_subjects = $total_subjects -1;
                $bangla_marks = round($combined_bangla_marks/2,2);
                 if($combined_bangla_marks>=66 && $bangla_both_theory>=46 && $bangla_both_mcq>=20 ){

                   if($bangla_marks>=80){
                       $ban_cgpa = 5.00;
                       $ban_grade = 'A+';
                       $total_cgpa = $total_cgpa + $ban_cgpa;
                   }elseif($bangla_marks >= 70 and $bangla_marks <= 79){
                       $ban_cgpa = 4.00;
                       $ban_grade = 'A';
                       $total_cgpa = $total_cgpa + $ban_cgpa;
                   }elseif($bangla_marks >= 60 and $bangla_marks <= 69){
                       $ban_cgpa = 3.50;
                       $ban_grade = 'A-';
                       $total_cgpa = $total_cgpa + $ban_cgpa;
                   }elseif($bangla_marks >= 50 and $bangla_marks <= 59){
                       $ban_cgpa = 3.00;
                       $ban_grade = 'B';
                       $total_cgpa = $total_cgpa + $ban_cgpa;
                   }elseif($bangla_marks >= 40 and $bangla_marks <= 49){
                       $ban_cgpa = 2.00;
                       $ban_grade = 'C';
                       $total_cgpa = $total_cgpa + $ban_cgpa;
                   }elseif($bangla_marks >= 33 and $bangla_marks <= 39){
                       $ban_cgpa = 1.00;
                       $ban_grade = 'D';
                       $total_cgpa = $total_cgpa + $ban_cgpa;
                   }else{
                       $ban_cgpa = 3.50;
                       $ban_grade = 'A-';
                       $total_cgpa = $total_cgpa + $ban_cgpa;
                   }

                 }else{
                      $ban_cgpa = 0.00;
                      $ban_grade = 'F';
                      $cgpa_status = 0;
                 }
        }


        if($combined_eng == 2){
                $total_subjects = $total_subjects -1;
                $eng_marks = round($combined_eng_marks/2,2);

               // dd($eng_marks);

                if($combined_eng_marks>65){

                   if($eng_marks>=80){
                       $eng_cgpa = 5.00;
                       $eng_grade = 'A+';
                       $total_cgpa = $total_cgpa + $eng_cgpa;
                   }elseif($eng_marks >= 70 and $eng_marks <= 79){
                       $eng_cgpa = 4.00;
                       $eng_grade = 'A';
                       $total_cgpa = $total_cgpa + $eng_cgpa;
                   }elseif($eng_marks >= 60 and $eng_marks <= 69){
                       $eng_cgpa = 3.50;
                       $eng_grade = 'A-';
                       $total_cgpa = $total_cgpa + $eng_cgpa;
                   }elseif($eng_marks >= 50 and $eng_marks <= 59){
                       $eng_cgpa = 3.00;
                       $eng_grade = 'B';
                       $total_cgpa = $total_cgpa + $eng_cgpa;
                   }elseif($eng_marks >= 40 and $eng_marks <= 49){
                       $eng_cgpa = 2.00;
                       $eng_grade = 'C';
                       $total_cgpa = $total_cgpa + $eng_cgpa;
                   }elseif($eng_marks >= 33 and $eng_marks <= 39){
                       $eng_cgpa = 1.00;
                       $eng_grade = 'D';
                       $total_cgpa = $total_cgpa + $eng_cgpa;
                   }else{
                       $eng_cgpa = 3.50;
                       $eng_grade = 'A-';
                       $total_cgpa = $total_cgpa + $eng_cgpa;
                   }

                 }else{
                      $eng_cgpa = 0.00;
                      $eng_grade = 'F';
                      $cgpa_status = 0;
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
                $gpa = "<strong style='color: #e66f57'> Failed </strong>";
            }
        } else {
            $gpa = "<strong style='color: #e66f57'> Failed </strong>";
        }
        $total_numbers = "<strong style='color: #67bf7e'>" . $total_marks . "</strong>";
        $cgpa = $cgpa_status == '1' ? "<strong style='color: #67bf7e'>" . $cgpa . "</strong>" : "<strong style='color: #e66f57'> Failed </strong>";

        @endphp

        @if(!$session_year->special)
            <div id="marks_table">
                <table id="table_1">
                    <thead class="divTableHeading">
                    <tr>
                        <th> Subject</th>
                        <th> Subject Marks</th>
                        <th> CA</th>
                        <th> Creative</th>
                        <th> Objective</th>
                        <th> Practical</th>
                        <th> Marks Obtained</th>
                        <th> Highest</th>
                        <th> Grade Point</th>
                        <th> Grade</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data['result'] as $row)
                        <tr>
                            <td class="sub_name">{{ $row->subject }} {{$row->subject_id == $row->optional_subject ? ' ( Optional Subject ) ' : '' }}</td>
                            <td>{{ $row->subject_marks}}</td>
                            <td>{{ $row->ctPMarks}}</td>
                            <td>{{ $row->theoryPMarks }}</td>
                            <td>{{ $row->mcq_marks }}</td>
                            <td>{{ $row->practical_marks }}</td>
                            <td>{{ $row->obtainedMark }}</td>
                            <td>{{ $row->highest_marks }}</td>
                            @if($row->subject_code == 101)
                                <td rowspan="2" style="vertical-align : middle;">{{ number_format($ban_cgpa,2) }}</td>
                                <td rowspan="2" style="vertical-align : middle;">{{ $ban_grade }}</td>
                            @elseif($row->subject_code == 107 )
                                <td rowspan="2" style="vertical-align : middle;">{{ number_format($eng_cgpa,2) }}</td>
                                <td rowspan="2" style="vertical-align : middle;">{{ $eng_grade }}</td>
                            @elseif($row->subject_code == 102 || $row->subject_code == 108 )

                            @else
                                <td>{{ $row->CGPA }}</td>
                                <td>{{ $row->grade }}</td>
                            @endif
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
                        <th> Others</th>
                        <th> Marks Obtained</th>
                        <th> Highest</th>
                        <th> Grade Point</th>
                        <th> Grade</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data['result'] as $row)
                        <tr>
                            <td class="sub_name">{{ $row->subject }} {{$row->subject_id == $row->optional_subject ? ' ( Optional Subject ) ' : '' }}</td>
                            <td>{{ $row->subject_marks}}</td>
                            <td>{{ $row->writtenMarks}}</td>
                            <td>{{ $row->assignmentMarks }}</td>
                            <td>{{ $row->otherMarks }}</td>
                            <td>{{ $row->obtainedMark }}</td>
                            <td>{{ $row->highest_marks }}</td>
                            @if($row->subject_code == 101)
                                <td rowspan="2" style="vertical-align : middle;">{{ number_format($ban_cgpa,2) }}</td>
                                <td rowspan="2" style="vertical-align : middle;">{{ $ban_grade }}</td>
                            @elseif($row->subject_code == 107 )
                                <td rowspan="2" style="vertical-align : middle;">{{ number_format($eng_cgpa,2) }}</td>
                                <td rowspan="2" style="vertical-align : middle;">{{ $eng_grade }}</td>
                            @elseif($row->subject_code == 102 || $row->subject_code == 108 )

                            @else
                                <td>{{ $row->CGPA }}</td>
                                <td>{{ $row->grade }}</td>
                            @endif
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
                    <td width="120px;">Total Marks</td>
                    <td width="10px;">:</td>
                    <td width="80px;">{!! $total_numbers !!} </td>
                    <td width="300px;" rowspan="10">Comments :</td>
                </tr>
                <tr>
                    <td>GPA</td>
                    <td>:</td>
                    <td>{!! $cgpa !!}</td>
                </tr>
                <tr>
                    <td>Grade</td>
                    <td>:</td>
                    <td>{!! $gpa !!}</td>
                </tr>
                <tr>
                    <td class="sub_name">Rank</td>
                    <td>:</td>
                    <td>{{ $data['rank'] }}</td>
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
        border: 1px solid #727b83;
        border-collapse: collapse;
        font-size: 12px;
        padding: 1px;
        overflow: hidden;
        line-height: 20px;
    }

    #table_1 {
        text-align: center;
        border: 1px solid #727b83;
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
        text-align: center;
    }

    #table_2 td:nth-child(1) {
        text-align: right;
    }

    #table_1 td:nth-child(1) {
        font-weight: bold;
    }

</style>
</body>

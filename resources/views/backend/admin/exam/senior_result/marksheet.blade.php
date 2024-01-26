@if(!empty($data))
    <div class="" id="marksheet">
        <div class="row" id="header_details">
            <div class="col-md-12 col-sm-12" style="margin-bottom: -18px;">
                <p style="text-align: center">
                    <img style="border: none;" src="{{ asset('assets/images/port_school_logo.png') }}"
                class="img-responsive img-thumbnail"
                width="70px"/>
                    <strong style="font-size:20px;">{{ $app_settings ? $app_settings->name : '' }}</strong>
                </p>
            </div>
            <div class="col-md-12 col-sm-12" >
                <p style="text-align: center">
                    <span style="margin-top:100px;"><strong style="font-size:17px;"> Exam : {{ $data['exam_name'] }} </strong></span>
                </p>
            </div>

            <div class="col-md-4 col-sm-12 pull-left">
                <p style="text-align: left"><strong>Student Name : {{ $data['student_name'] }}</strong> <br/>
                    <strong>Class : </strong> {{ $data['class_name'] }} &nbsp;&nbsp; <strong>Section : </strong> {{ $data['section'] }} &nbsp;&nbsp; <strong>Group : </strong> {{ $data['group'] }}<br/>
                    <strong>Roll : </strong> {{ $data['std_roll'] }}
                </p>
            </div>
            <div class="col-md-4 col-sm-12">
                <p style="text-align: center">
                    &nbsp;
                </p>
            </div>
            <div class="col-md-4 col-sm-12 pull-right">
                <p style="text-align: right">
                    <strong>Student's ID : </strong>{{ $data['student_code']}}<br/>
                    {{-- <strong> Session : </strong>{{ $data['year'] }} --}}
                </p>
            </div>
        </div>
        @php
            $total_marks = 0;
            $cgpa_status = 1;
            $total_gpa = 0;
            $total_cgpa = 0;
            $optional_sub_marks = 0;
            $total_subjects = 0;

            $bangla_marks = 0;
            $combined_bangla = 0;
            $combined_bangla_marks = 0;
            $bangla_both_theory = 0;
            $bangla_both_mcq = 0;

            $ban_cgpa = $ban_grade = 0;

            $eng_marks = 0;
            $combined_eng = 0;
            $combined_eng_marks = 0;

            $eng_cgpa = $eng_grade = 0;


            $total_subjects = count($data['result']);

        foreach($data['result'] as $row) {
            // dd($row);


          $total_marks+= $row->obtainedMark;

           if ($row->grade === 'F' && $row->subject_code != 101 && $row->subject_code != 102 && $row->subject_code != 107 && $row->subject_code != 108  && $row->subject_id != $row->optional_subject) {
               $cgpa_status = 0;
            }

           if ($row->grade === 'F' && $row->subject_code != 101 && $row->subject_code != 102 && $row->subject_code != 107 && $row->subject_code != 108 && $row->subject_id != $row->optional_subject) {
               $cgpa_status = 0;
            }

            if ($cgpa_status != 0) {
              $total_cgpa = round($total_cgpa + $row->CGPA, 2);
            }

            //special check
            if(!$session_year->special){
                // Bangla combined
                if ($row->subject_code == 101 || $row->subject_code == 102) {
                    $combined_bangla = $combined_bangla + 1;
                    $combined_bangla_marks = $combined_bangla_marks + $row->obtainedMark;
                    $total_cgpa = $total_cgpa - $row->CGPA;
                    $bangla_both_theory = $bangla_both_theory + $row->theoryPMarks;
                    $bangla_both_mcq = $bangla_both_mcq + $row->mcqPMarks;
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
                $total_subjects = $total_subjects -1; // both are now 1 subject so 1 minus from total subject
                $bangla_marks = ceil($combined_bangla_marks/2);

                // need total 66 to pass the subject
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
                $total_subjects = $total_subjects -1; // both are now 1 subject so 1 minus from total subject
                $eng_marks = ceil($combined_eng_marks/2);

                if($combined_eng_marks>=66){

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
                $gpa = "<strong style='color: #e02902'> Failed </strong>";
            }
        } else {
            $gpa = "<strong style='color: #e02902'> Failed </strong>";
        }
        // $total_numbers = "<strong style='color: #67bf7e'>" . $total_marks . "</strong>";
        // $cgpa = $cgpa_status == '1' ? "<strong style='color: #67bf7e'>" . $cgpa . "</strong>" : "<strong style='color: #e66f57'> Failed </strong>";
        $total_numbers = "<strong style='color: black;'>" . $total_marks . "</strong>";
        $cgpa = $cgpa_status == '1' ? "<strong style='color:black;'>" . $cgpa . "</strong>" : "<strong style='color: #e02902'> 0.00 </strong>";


        @endphp


        <div class="row">
            <div class="col-md-12 col-sm-12 table-responsive">
                <!--special check-->
                @if(!$session_year->special)
                    <table id="manage_all_result" class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th style="text-align: left"> Subject</th>
                            <th> Subject Marks</th>
                            {{-- <th> CA</th> --}}
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
                                {{-- <td>{{ $row->ctPMarks}}</td> --}}
                                <td>{{ $row->theoryPMarks }}</td>
                                <td>{{ $row->mcqPMarks }}</td>
                                <td>{{ $row->practPmarks }}</td>
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
                        <tfoot style="border-top: 2px solid black; display: none;">
                        <tr>
                            <td style="color: black; text-align: left;">Total Marks</td>
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
                @else
                    <table id="manage_all_result" class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th style="text-align: left"> Subject</th>
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
                                <td class="sub_name" style="font-weight: bold;">{{ $row->subject }} {{$row->subject_id == $row->optional_subject ? ' ( Optional Subject ) ' : '' }}</td>
                                <td>{{ $row->subject_marks}}</td>
                                <td>{{ $row->writtenMarks}}</td>
                                <td>{{ $row->assignmentMarks }}</td>
                                <td>{{ $row->otherMarks }}</td>
                                <td>{{ $row->obtainedMark }}</td>
                                <td>{{ $row->highest_marks }}</td>
                                {{-- @if($row->subject_code == 101)
                                    <td rowspan="2" style="vertical-align : middle;">{{ number_format($ban_cgpa,2) }}</td>
                                    <td rowspan="2" style="vertical-align : middle;">{{ $ban_grade }}</td>
                                @elseif($row->subject_code == 107 )
                                    <td rowspan="2" style="vertical-align : middle;">{{ number_format($eng_cgpa,2) }}</td>
                                    <td rowspan="2" style="vertical-align : middle;">{{ $eng_grade }}</td>
                                @elseif($row->subject_code == 102 || $row->subject_code == 108 )

                                @else
                                    <td>{{ $row->CGPA }}</td>
                                    <td>{{ $row->grade }}</td>
                                @endif --}}
                                <td>{{ $row->CGPA }}</td>
                                <td>{{ $row->grade }}</td>
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
                @endif
            </div>
        </div>
        <div class="row">
            <div class="col-md-7 col-sm-12 table-responsive">
                <table id="std_inf" class="table table-bordered" cellspacing="2px">
                    <tr>
                        <td width="120px;" class="sub_name" style="font-weight:bold;">Total Marks Obtained</td>
                        <td width="10px;">:</td>
                        <td width="80px;">{!! $total_numbers !!} </td>
                        <td width="150px;" rowspan="10">Comments :</td>
                    </tr>
                    <tr>
                        <td class="sub_name" style="font-weight:bold;">GPA</td>
                        <td>:</td>
                        <td>{!! $cgpa !!}</td>
                    </tr>
                    <tr>
                        <td class="sub_name" style="font-weight:bold;">Grade</td>
                        <td>:</td>
                        <td>{!! $gpa !!}</td>
                    </tr>

                    <tr>
                        <td class="sub_name" style="font-weight:bold;">Class Rank</td>
                        <td>:</td>
                        <td>{{ $data['class_rank'] }}</td>
                    </tr>
                    <tr>
                        <td class="sub_name" style="font-weight:bold;">Section Rank</td>
                        <td>:</td>
                        <td>{{ $data['rank'] }}</td>
                    </tr>

                    <tr>
                        <td class="sub_name" style="font-weight:bold;">Total Students</td>
                        <td>:</td>
                        <td><input type="number" id="total_std" min="0" value="{{ $data['total_student'] }}"/></td>
                    </tr>
                    <tr>
                        <td class="sub_name" style="font-weight:bold;">Total Working Days</td>
                        <td>:</td>
                        <td><input type="number" id="total_wd" min="0" /></td>
                    </tr>
                    <tr>
                        <td class="sub_name" style="font-weight:bold;">Total Attendance</td>
                        <td>:</td>
                        <td><input type="number" id="total_atd" min="0"/></td>
                    </tr>
                    <tr>
                        <td class="sub_name" style="font-weight:bold;">Parent's Signature</td>
                        <td>:</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="sub_name" style="font-weight:bold;">Class Teacher's Signature</td>
                        <td>:</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="sub_name" style="font-weight:bold;">Head Teacher's Signature</td>
                        <td>:</td>
                        <td><img style="border: none;" src="{{ asset('assets/images/new_head_teacher_signature.jpeg') }}"
                                 class="img-responsive img-thumbnail"
                                 width="50px"/>
                        </td>
                    </tr>
                </table>
            </div>
            <div id="marks_distribution" class="col-md-4">
                <table id="marks_inf" class="table table-bordered">
                    <thead>
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
    </div>
    <hr/>
    <div class="col-md-12">
        <button type='button' id='btn' class='btn btn-success pull-right' value='Print' target="_blank"
                onClick='exportMarksheet();'>Print Marksheet
        </button>
    </div>
    <div class="clearfix"></div>
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
    th {
        text-transform: uppercase;
    }

    #header_details p {
        font-size: 14px;
    }

    #manage_all_result td, th {
        text-align: center;
        font-size: 11px;
    }

    #manage_all_result td.sub_name {
        text-align: left;
    }

    #marks_inf td, th {
        text-align: center;
        font-size: 11px;
    }

    #std_inf td, th {
        font-size: 11px;
    }

    #std_inf td.sub_name {
        text-align: right;
    }

    input {
        border: 1px solid #f6f6f6;
    }

    @media screen and (min-width: 768px) {

        #marks_distribution {
            border-left: 1px solid #e9e9e9;
        }

    }

    #manage_all_result th {
        text-align: center;
    }
</style>
<link href="{{ asset('/assets/css/marksheet.css') }}" type="text/css" rel="stylesheet" media="print">
<script>
    function printMarksheet() {
        $('#marksheet').printThis({
            importCSS: true,
            importStyle: true,//thrown in for extra measure
            loadCSS: "{{ asset('/assets/css/bootstrap.min.css') }}",
            //header: '<h1> Table Report</h1>',

        });
    }

    function exportMarksheet() {

        var class_id = "{{$data['class_id']}}";
        var student_code = "{{$data['student_code']}}";
        var section_id = "{{$data['section_id']}}";
        var exam_id = "{{$data['exam_id']}}";

        var class_name = "{{$data['class_name']}}";
        var section_name = "{{$data['section_name']}}";
        var exam_name = "{{$data['exam_name']}}";
        var student_name = "{{$data['student_name']}}";
        var std_roll = "{{$data['std_roll']}}";
        var rank = "{{$data['rank']}}";
        var class_rank = "{{$data['class_rank']}}";


        var total_std = $('#total_std').val();
        var total_atd = $('#total_atd').val();
        var total_wd = $('#total_wd').val();
        var position = $('#position').val();


        var base = '{!! route('srPrintMarksheet.access') !!}';
        var url = base + '?class_id=' + class_id + '&section_id=' + section_id + '&student_code=' + student_code
            + '&exam_id=' + exam_id + '&class_name=' + class_name + '&section_name=' + section_name
            + '&exam_name=' + exam_name + '&student_name=' + student_name + '&std_roll=' + std_roll + '&rank=' + rank+ '&class_rank=' + class_rank
            + '&total_std=' + total_std + '&total_atd=' + total_atd + '&total_wd=' + total_wd + '&position=' + position;
        // window.location.href = url;
        window.open(url);
    }
</script>

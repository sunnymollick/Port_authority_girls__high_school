@if(!empty($data))
    <div class="" id="marksheet">
        <div class="row" id="header_details">
            <div class="col-md-4 col-sm-12 pull-left">
                <p style="text-align: left"><strong>Name : {{ $data['student_name'] }}</strong> <br/>
                    <strong>Class : </strong> {{ $data['class_name'] }} <br/>
                    <strong>Roll : </strong> {{ $data['std_roll'] }}
                </p>
            </div>
            <div class="col-md-4 col-sm-12">
                <p style="text-align: center">
                    <strong>{{ $app_settings ? $app_settings->name : '' }}</strong>
                    <br/>
                    <strong> Exam : {{ $data['exam_name'] }} </strong>
                </p>
            </div>
            <div class="col-md-4 col-sm-12 pull-right">
                <p style="text-align: right">
                    <strong>Student's ID : </strong>{{ $data['student_code']}}<br/>
                    <strong> Session : </strong>{{ $data['year'] }}
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
                $gpa = "<strong style='color: #e66f57'> Failed </strong>";
            }
        } else {
            $gpa = "<strong style='color: #e66f57'> Failed </strong>";
        }
        $total_numbers = "<strong style='color: #67bf7e'>" . $total_marks . "</strong>";
        $cgpa = $cgpa_status == '1' ? "<strong style='color: #67bf7e'>" . $cgpa . "</strong>" : "<strong style='color: #e66f57'> Failed </strong>";

        @endphp


        <div class="row">
            <div class="col-md-12 col-sm-12 table-responsive">
                <table id="manage_all_result" class="table table-bordered table-hover">
                    <thead>
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
        </div>
        <div class="row">
            <div class="col-md-8 col-sm-12 table-responsive">
                <table id="std_inf" class="table table-bordered" cellspacing="2px">
                    <tr>
                        <td width="120px;" class="sub_name">Total Marks</td>
                        <td width="10px;">:</td>
                        <td width="80px;">{!! $total_numbers !!} </td>
                        <td width="300px;" rowspan="9">Comments :</td>
                    </tr>
                    <tr>
                        <td class="sub_name">Grade Point Average</td>
                        <td>:</td>
                        <td></td>
                        {{--{!! $cgpa !!}--}}
                    </tr>
                    <tr>
                        <td class="sub_name">Grade</td>
                        <td>:</td>
                        <td></td>
                        {{--{!! $gpa !!}--}}
                    </tr>
                    <tr>
                        <td class="sub_name">Total Students</td>
                        <td>:</td>
                        <td><input type="number" id="total_std" min="0" value="{{ $data['total_student'] }}"/></td>
                    </tr>
                    <tr>
                        <td class="sub_name">Total Working Days</td>
                        <td>:</td>
                        <td><input type="number" id="total_wd" min="0" value="240"/></td>
                    </tr>
                    <tr>
                        <td class="sub_name">Total Attendance</td>
                        <td>:</td>
                        <td><input type="number" id="total_atd" min="0"/></td>
                    </tr>
                    {{--<tr>--}}
                    {{--<td class="sub_name">Position</td>--}}
                    {{--<td>:</td>--}}
                    {{--<td><input type="number" id="position" min="0"/></td>--}}
                    {{--</tr>--}}
                    <tr>
                        <td class="sub_name">Parent's Signature</td>
                        <td>:</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="sub_name">Teacher's Signature</td>
                        <td>:</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="sub_name">Head Teacher's Signature</td>
                        <td>:</td>
                        <td><img style="border: none;" src="{{ asset('assets/images/head_master.png') }}"
                                 class="img-responsive img-thumbnail"
                                 width="50px"/>
                        </td>
                    </tr>
                </table>
            </div>
            <div id="marks_distribution" class="col-md-3">
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
        <button type='button' id='btn' class='btn btn-success pull-right' value='Print'
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

        var total_std = $('#total_std').val();
        var total_atd = $('#total_atd').val();
        var total_wd = $('#total_wd').val();
        var position = $('#position').val();


        var base = '{!! route('printMarksheet.access') !!}';
        var url = base + '?class_id=' + class_id + '&section_id=' + section_id + '&student_code=' + student_code
            + '&exam_id=' + exam_id + '&class_name=' + class_name + '&section_name=' + section_name
            + '&exam_name=' + exam_name + '&student_name=' + student_name + '&std_roll=' + std_roll
            + '&total_std=' + total_std + '&total_atd=' + total_atd + '&total_wd=' + total_wd + '&position=' + position;
        window.location.href = url;
    }
</script>

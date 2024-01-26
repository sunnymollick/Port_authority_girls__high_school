<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <style type="text/css">
    h1, h2, h3, h4, h5, h6, p{
        margin: 0;
    }
    td{
        padding: 2px;
    }
        .heading-text{
            text-align: center;
            width: 85%;
            float: left;
        }

        .picBorder{
            width: 15%;
            float: right;
            border: 1px solid black;
        }
        .picBorder p{
            padding: 40px;
        }

        #main .start{
            margin-top: 120px;
        }

        #main-2 .start{
            margin-top: 120px;
        }

        #class_teacher_signature{
            width: 70%;
            float: left;
        }

        #head_teacher_signature{
            /* width: 40%; */
            float: right;
        }

    </style>
</head>
<body style="page-break-after: auto">
@php
    $i = 1;
@endphp
@if(($data) )
    <div id="marks_pdf">
       
        <div id="heading">
            <div class="heading-text">
                <h4>Chattogram Port Authority Girls High School</h4>
                <h5>Bondhor, Chattogram</h5>
                <h4>Admission Form</h4>
            </div>

            <div class="picBorder">
                <p>Photo</p>
            </div>
        </div>

        <div id="main">
            <div class="start">
                <p>Serial No: {{ $data->ref_id }}</p>
                <table style="width: 100%">
                    <tr>
                        <td><span style="font-weight: bold">The class that student wants to be admitted:</span> {{ $data->enrollment_class }}</td>
                        <td><span style="font-weight: bold">Mobile No:</span> {{ $data->phone_no }}</td>
                    </tr>
                    <tr>
                        <td colspan="2"><span style="font-weight: bold">Girl's Name:</span> {{ $data->girl_name }}</td>
                    </tr>
                    <tr>
                        <td><span style="font-weight: bold">Father's Name: </span>{{ $data->father_name }}</td>
                        <td><span style="font-weight: bold">Mother's Name: </span> {{ $data->mother_name }}</td>
                    </tr>
                    <tr>
                        <td><span style="font-weight: bold">Village: </span> {{ $data->vill }}</td>
                        <td><span style="font-weight: bold">Post Office: </span> {{ $data->po }}</td>
                    </tr>
                    <tr>
                        <td><span style="font-weight: bold">Police Station: </span>{{ $data->ps }}</td>
                        <td><span style="font-weight: bold">District: </span> {{ $data->dist }}</td>
                    </tr>
                    <tr>
                        <td colspan="2"><span style="font-weight: bold">Whether the student is dependent on an officer/employee of the Chittagong Port Authority:</span> {{ $data->depended_on_port_authority }}</td>
                    </tr>
                    <tr>
                        <td><span style="font-weight: bold">Date of Birth(In Number):</span> {{ $data->dob }}</td>
                        <td><span style="font-weight: bold">Date of Birth(In Text):</span> {{ $data->dob_text }}</td>
                    </tr>
                    <tr>
                        <td colspan="2"><span style="font-weight: bold">The student is dependent on the guardian his/Her Name: </span>{{ $data->guardian_name }}</td>
                    </tr>
                    <tr>
                        <td colspan="2"><span style="font-weight: bold">Name of Guardian Father's Name: </span> {{ $data->guardian_father_name }}</td>
                    </tr>
                    
                    <tr>
                        <td colspan="2"><span style="font-weight: bold">Guardian Present Address:</span> {{ $data->guardian_present_address }}</td>
                    </tr>
                    <tr>
                        <td><span style="font-weight: bold">Student's relationship with guardian: </span>{{ $data->relation_with_guardian }}</td>
                        <td><span style="font-weight: bold">Address of work: </span> {{ $data->guardian_work_address }}</td>
                    </tr>
                    <tr>
                        <td><span style="font-weight: bold">Work Designation: </span>{{ $data->guardian_work_designation }}</td>
                        <td><span style="font-weight: bold">Salary Scale: </span> {{ $data->guardian_salary_scale }}</td>
                    </tr>
                    <tr>
                        <td><span style="font-weight: bold">Monthly Basic Salary: </span>{{ $data->guardian_monthly_salary }}</td>
                        <td><span style="font-weight: bold">Next date of annual pay increment: </span> {{ $data->guardian_salary_next_increment_date }}</td>
                    </tr>
                    <tr>
                        <td colspan="2"><span style="font-weight: bold">Nationality: </span>{{ $data->nationality }}</td>
                    </tr>

                    <tr>
                        <td colspan="2"><span style="font-weight: bold">Student's special qualities (sports, music, scouting etc. certificate to be attached): </span> {{ $data->speciality }}</td>
                    </tr>

                    <tr>
                        <td colspan="2"><span style="font-weight: bold">School Name of student came from: </span>{{ $data->school_name }}</td>
                    </tr>

                    <tr>
                        <td><span style="font-weight: bold">Village: </span>{{ $data->school_vill }}</td>
                        <td><span style="font-weight: bold">Post Office: </span>{{ $data->school_po }}</td>
                    </tr>

                    <tr>
                        <td><span style="font-weight: bold">Police Station: </span>{{ $data->school_ps }}</td>
                        <td><span style="font-weight: bold">District: </span>{{ $data->school_dist }}</td>
                    </tr>

                    <tr>
                        <td colspan="2"><span style="font-weight: bold">Whether admission through clearance: </span>{{ $data->is_admit_by_tc }}</td>
                    </tr>
                    
                </table>

            </div>
            
        </div>

        <p>- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - </p>

        <div id="heading">
            <div class="heading-text">
                <h4>Chattogram Port Authority Girls High School</h4>
                <h5>Bondhor, Chattogram</h5>
                <h4>Admit Card</h4>
                <h6>Admission Test - (Academic Year 2023)</h6>
            </div>

            <div class="picBorder">
                <p>Photo</p>
            </div>
        </div>

        <div id="main-2">
            <div class="start">
                <p>Serial No: {{ $data->ref_id }}</p>
                <table style="width: 100%">
                    <tr>
                        <td><span style="font-weight: bold">Date of Admission Test: </span></td>
                        <td><span style="font-weight: bold">Time: </span></td>
                    </tr>
                    <tr>
                        <td colspan="2"><span style="font-weight: bold">Girl's Name:  </span>{{ $data->girl_name }}</td>
                    </tr>
                    <tr>
                        <td><span style="font-weight: bold">Class: </span>{{ $data->enrollment_class }}</td>
                        <td><span style="font-weight: bold">Roll No: </span></td>
                    </tr>
                </table>
            </div>

            <br>

            <div id="class_teacher_signature">
                <p>_____________________</p>
                <p>Class Teacher</p>
                <p>Date: _______________</p>
            </div>

            <div id="head_teacher_signature">
                <p>_____________________</p>
                <p>Head Teacher</p>
                <p>Date: _______________</p>
            </div>
            <p>- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - </p>

            <div class="declaration"></div>
            <P style="text-align: center">Declaration</P>
            <ol>
                <li>Admit card must be brought with you.</li>
                <li>Must be present in the examination hall at least 15 minutes before the exam start.</li>
                <li>Disciplinary action will be taken if malpractices in the examination.</li>
                <li>Bringing anything other than the relevant equipment to the examination will be considered as inadmissibility.</li>
            </ol>
            
        </div>

        
    </div>
@endif
</body>
</html>
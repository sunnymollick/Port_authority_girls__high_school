<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <style type="text/css">
        #invoice {
            width: 100%;
            display: block;
            padding: 20px;
            font-family: sans-serif, Arial, Verdana, "Trebuchet MS";
            border: 3px dotted #808991;
        }

        #heading {
            text-align: center;
        }

        #para {
            text-align: center;
            line-height: 5px;
        }

        #instruction {
            clear: both;
            width: 100%;
            line-height: 25px;
        }

        .col-md-10 {
            width: 70%;
            float: left;
            position: relative;
            line-height: 30px;
        }

        .col-md-2 {
            width: 20%;
            float: left;
            margin-right: 20px;
            position: relative;
        }

        .text-bold {
            width: 200px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 15px;
        }

    </style>
</head>
<body style="page-break-after: auto">
<div id="invoice">
    <div id="heading">
        <img src="{{ asset('assets/images/school_logo.png') }}" width="50%"/>
        <hr/>
    </div>
    <div id="para">
        <h2>Admit Card</h2>
        <h4>{{$std->exam_name}}</h4>
        <p>{{ ' From : ' .  $std->start_date . ' To :  ' . $std->end_date}}</p>
    </div>
    <div class="col-md-10">
        <table>
            <tr>
                <td class="text-bold">Applicant's Name</td>
                <td>:</td>
                <td>{{ $std->student_name }}</td>
            </tr>
            <tr>
                <td class="text-bold">Applicant's ID</td>
                <td>:</td>
                <td>{{ $std->std_code }}</td>
            </tr>
            <tr>
                <td class="text-bold">Father's Name</td>
                <td>:</td>
                <td>{{ $std->father_name }}</td>
            </tr>
            <tr>
                <td class="text-bold">Mother's Name</td>
                <td>:</td>
                <td>{{ $std->mother_name }}</td>
            </tr>
            <tr>
                <td class="text-bold">Class</td>
                <td>:</td>
                <td>{{ $std->class_name }}</td>
            </tr>
            <tr>
                <td class="text-bold">Section</td>
                <td>:</td>
                <td>{{ $std->section_name }}</td>
            </tr>
            <tr>
                <td class="text-bold">Applicant's Roll</td>
                <td>:</td>
                <td>{{ $std->roll }}</td>
            </tr>
            <tr>
                <td class="text-bold">Exam Name</td>
                <td>:</td>
                <td>{{ $std->exam_name }}</td>
            </tr>
        </table>
    </div>
    <div class="col-md-2 col-sm-12">
        <img style="border: 5px solid #edefec; margin-top: 20px" src="{{ asset($std->file_path) }}" width="180px"/>
    </div>
    <div id="instruction">
        <h4>General Instructions</h4>
        <ul>
            <li>Each candidate must bring the printed copy of this admit card in the exam hall</li>
            <li>Candidate should be present in the exam center 30 minutes before the exam starts</li>
            <li>Carrying any kind of electronic devices like phone is strongly prohibited</li>
        </ul>

    </div>
</div>
</body>




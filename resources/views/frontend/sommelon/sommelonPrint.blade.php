<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>
<body style="page-break-after: auto">
@if(!empty($data))
    <div class="" id="alumni_application">
        <div id="row_block">
            <div class="col-md-2">
                <img src="{{asset('assets/images/Port-girls-school.png')}}" width="115px">
            </div>
            <div class="col-md-8">
                <div style="line-height: 7px;">
                    <h3 style="text-transform: uppercase; font-weight: 700">Former/Current Students Alumni</h3>
                    <h4 style="text-transform: uppercase; font-weight: 500">Chittagong Port Authority Girls High
                        School</h4>
                    <h4 style="text-transform: uppercase;font-weight: 400; margin-top: 10px">Registration Form</h4>
                </div>
            </div>
            <div class="col-md-2">
                @if($data->file_path)
                    <img src="{{asset($data->file_path)}}" width="100px">
                @else
                    <img src="{{asset('assets/images/passport.png')}}" class="img img-thumbnail" width="100px">
                @endif
            </div>
            <br/><br/>
        </div>
        <div id="row_block" style="padding-top: 10px">
            <div class="col-md-4">Serial Number : {{$data->sl}}</div>
            <div class="col-md-4">Registration Number : {{$data->reg_no}}</div>
            <div class="col-md-4" style="text-align: right">Registration Date : {{$data->reg_date}}</div>
        </div>
        <div id="row_block">
            <div class="col-md-12">
                <h5 style="line-height: 25px;font-family: 'PT Sans'; font-size: 16px"> I am a former/current student of
                    Chittagong Port Authority Girls High School.
                    I would like to list my name by promising to abide by the rules of this conference.
                </h5>
            </div>
        </div>
        <div id="body_block">
            <div id="row_block">
                <div class="col-md-6">Name : {{$data->std_name}}</div>
                <div class="col-md-6">SSC Batch : {{$data->ssc_batch}}</div>
            </div>
            <div id="row_block">
                <div class="col-md-6">Father's Name : {{$data->std_father_name}}</div>
                <div class="col-md-6">Mother's Name : {{$data->std_mother_name}}</div>
            </div>
            <div id="row_block">
                <div class="col-md-6">Email : {{$data->email}}</div>
                <div class="col-md-6">Mobile : {{$data->mobile}}</div>
            </div>
            <div id="row_block">
                <div class="col-md-6">Present Address : {{$data->prs_address}}</div>
                <div class="col-md-6">Parmanent Address : {{$data->prm_address}}</div>
            </div>
            <div id="row_block">
                <div class="col-md-6">Date of Birth : {{$data->std_dob}}</div>
                <div class="col-md-6">Blood Group : {{$data->blood_group}}</div>
            </div>
            <div id="row_block">
                <div class="col-md-6">University Name : {{$data->university_name}}</div>
                <div class="col-md-6">Session : {{$data->session}}</div>
            </div>
            <div id="row_block">
                <div class="col-md-6">Educational Qulification : {{$data->education}}</div>
                <div class="col-md-6">Profession : {{$data->profession}}</div>
            </div>
            <div id="row_block">
                <div class="col-md-12">Designation & Current working place : {{$data->designation_work_place}}</div>
            </div>
            <div id="row_block">
                <div class="col-md-6">Bikash Transaction ID : {{$data->bikash_trans_id}}</div>
                <div class="col-md-6">Bikash Transaction Date : {{$data->bikash_trans_date}}</div>
            </div>
        </div>
        <div id="row_block">
            <br/><br/>
            <hr/>
            <br/><br/>
            <div class="col-md-2">
                <img src="{{asset('assets/images/Port-girls-school.png')}}" width="100px">
            </div>
            <div class="col-md-7" style="line-height: 3px;">
                <h3 style="text-transform: uppercase;font-weight: 700; margin-top: 10px">Applicant Section</h3>
                <h4 style="text-transform: uppercase; font-weight: 600">Former/Current Students Alumni</h4>
                <h5 style="text-transform: uppercase; font-weight: 500">Chittagong Port Authority Girls High
                    School</h5>
            </div>
            <div class="col-md-3" style="line-height: 3px;">
                <p>Serial Number : {{$data->sl}}</p>
                <p>Registration Number : {{$data->reg_no}}</p>
                <p>Registration Date : {{$data->reg_date}}</p>
            </div>
        </div>
        <div id="row_block">
            <div class="col-md-8" style="line-height: 2px;"><h4>1000 Tk was received with thanks</h4></div>
        </div>
        <div id="row_block">
            <div class="col-md-9" style="line-height: 5px;">
                <p>Student Name : {{$data->std_name}}</p>
                <p>Conference date : 24th January 2020</p>
                <p>Time : From 7AM to 6PM</p>
                <p>Place : CPAGHS Campus</p>
            </div>
            <div class="col-md-3" style="line-height: 18px;">
                <h5 style="text-transform: uppercase;font-weight: 700; margin-top: 10px">Receiver Signature</h5>
                <img src="{{asset('assets/images/head_master.png')}}" width="60px">
                <h5 style="text-transform: uppercase; font-weight: 500">Head Teacher</h5>
            </div>
        </div>
        <div id="row_block">
            <div class="col-md-12" style="text-align: center">
                <p>Contact: Masmima Momtaj, Rubaiyat Jahan/Chittagong Port Authority Girls High School, Chittagong-4100
                    |Phone:01825856667,01718312513|Email : info@cpaghs.edu.bd</p>
            </div>
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

    #alumni_application {
        padding-right: 15px;
    }

    #body_block {
        margin-left: 10px;
    }

    #row_block {
        width: 100%;
        display: block;
        clear: both;
        font-size: 16px;
        padding-bottom: 15px;
    }

    .col-md-8 {
        width: 70%;
        float: left;
        text-align: center;
    }

    .col-md-2 {
        width: 15%;
        float: left;
    }

    .col-md-12 {
        width: 100%;
        float: left;
    }

    .col-md-4 {
        width: 33.33%;
        float: left;
        line-height: 25px;
    }

    .col-md-6 {
        width: 50%;
        float: left;
        line-height: 25px;
    }

    .col-md-7 {
        width: 55%;
        float: left;
        line-height: 25px;
        text-align: center;
    }

    .col-md-9 {
        width: 70%;
        float: left;
        line-height: 25px;

    }

    .col-md-3 {
        width: 30%;
        float: left;
        line-height: 25px;
    }
</style>
</body>

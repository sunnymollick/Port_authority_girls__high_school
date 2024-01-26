<div class="row">
    <div class="col-md-12">
        <!-- Custom Tabs -->
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#student" data-toggle="tab" aria-expanded="true">Students Information</a>
                </li>
                <li class=""><a href="#parent" data-toggle="tab" aria-expanded="false">Parent Information</a></li>
                <li class=""><a href="#idcard" data-toggle="tab" aria-expanded="false">ID Card</a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="student">
                    <div class="col-md-8 col-sm-12 table-responsive">
                        <table id="view_details" class="table table-bordered table-hover">
                            <tbody>
                            <tr>
                                <td class="subject"> Student's Name</td>
                                <td> :</td>
                                <td> {{ $student->name }} </td>
                            </tr>
                            <tr>
                                <td class="subject"> Class</td>
                                <td> :</td>
                                <td> {{ $enroll->stdclass->name }} </td>
                            </tr>
                            <tr>
                                <td class="subject"> Section</td>
                                <td> :</td>
                                <td> {{ $enroll->section->name }} </td>
                            </tr>
                            <tr>
                                <td class="subject"> Roll</td>
                                <td> :</td>
                                <td> {{ $enroll->roll }} </td>
                            </tr>
                            <tr>
                                <td class="subject"> Optional Subject</td>
                                <td> :</td>
                                <td> {{ $enroll->subject ? $enroll->subject->name : 'Not Selected' }} </td>
                            </tr>
                            <tr>
                                <td class="subject"> Gender</td>
                                <td> :</td>
                                <td> {{ $student->gender }} </td>
                            </tr>
                            <tr>
                                <td class="subject"> Date of Birth</td>
                                <td> :</td>
                                <td> {{ $student->dob }} </td>
                            </tr>
                            <tr>
                                <td class="subject"> Phone</td>
                                <td> :</td>
                                <td> {{ $student->phone }} </td>
                            </tr>
                            <tr>
                                <td class="subject"> Email</td>
                                <td> :</td>
                                <td> {{ $student->email }} </td>
                            </tr>
                            <tr>
                                <td class="subject"> Address</td>
                                <td> :</td>
                                <td> {{ $student->address }}</td>
                            </tr>
                            <tr>
                                <td class="subject"> Blood Group</td>
                                <td> :</td>
                                <td> {{ $student->blood_group }} </td>
                            </tr>
                            <tr>
                                <td class="subject"> Parents's ID</td>
                                <td> :</td>
                                <td> {{ $student->parent_id }} </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-4 col-sm-12 short_inf">
                        <img src="{{ asset($student->file_path) }}" class="img-responsive img-thumbnail"
                             width="200px"/><br/><br/>
                        Name : {{ $student->name  }} <br/>
                        Student's Code : {{ $student->std_code  }} <br/>
                        Class : {{ $enroll->stdclass->name }} , Roll : {{ $enroll->roll  }} <br/>
                        Section : {{ $enroll->section->name }} <br/>
                        Phone : {{ $student->phone  }}
                    </div>
                </div>
                <div class="clearfix"></div>
                <!-- /.tab-pane -->
                <div class="tab-pane" id="parent">
                    @if($parent)
                        <div class="col-md-8 col-sm-12 table-responsive">
                            <table id="view_details" class="table table-bordered table-hover">
                                <tbody>
                                <tr>
                                    <td class="subject"> Father's Name</td>
                                    <td> :</td>
                                    <td> {{ $parent->father_name }} </td>
                                </tr>
                                <tr>
                                    <td class="subject"> Mother's Name</td>
                                    <td> :</td>
                                    <td> {{ $parent->mother_name }} </td>
                                </tr>
                                <tr>
                                    <td class="subject"> Parents's ID</td>
                                    <td> :</td>
                                    <td> {{ $parent->parent_code }} </td>
                                </tr>
                                <tr>
                                    <td class="subject"> Gender</td>
                                    <td> :</td>
                                    <td> {{ $parent->gender }} </td>
                                </tr>
                                <tr>
                                    <td class="subject"> Phone</td>
                                    <td> :</td>
                                    <td> {{ $parent->phone }} </td>
                                </tr>
                                <tr>
                                    <td class="subject"> Email</td>
                                    <td> :</td>
                                    <td> {{ $parent->email }} </td>
                                </tr>
                                <tr>
                                    <td class="subject"> Profession</td>
                                    <td> :</td>
                                    <td> {{ $parent->profession }}</td>
                                </tr>
                                <tr>
                                    <td class="subject"> Blood Group</td>
                                    <td> :</td>
                                    <td> {{ $parent->blood_group }} </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-4 col-sm-12">
                            <img src="{{ asset($parent->file_path) }}" class="img-responsive img-thumbnail"
                                 width="200px"/><br/><br/>
                            Name : {{ $parent->father_name  }} <br/>
                            ID : {{ $parent->parent_code  }} <br/>
                            Phone : {{ $parent->phone  }} <br/>
                        </div>
                    @endif
                </div>
                <div class="clearfix"></div>
                <!-- /.tab-pane -->

                <div class="tab-pane" id="idcard">
                    <div class="col-md-offset-3 col-md-4 col-sm-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card_logo">
                                    <img src="{{ asset('assets/images/favicon.png') }}" width="80px"/>
                                </div>
                                <div class="clearfix"></div>
                                <div class="col-md-12 text-center">
                                    <strong class="text-uppercase school_title"
                                            style="font-size: 11px">{{ $app_settings->name }}</strong>
                                    <strong class="text-uppercase school_address"
                                            style="font-size: 11px">{{ $app_settings->address }}</strong>
                                </div>
                                <div class="clearfix"></div>
                                <div class="std_img">
                                    <img src="{{ asset($student->file_path) }}" class="img-responsive img-thumbnail"
                                         width="100px"/>
                                </div>
                                <div class="clearfix"></div>
                                <div class="col-md-12 text-center">
                                    <strong class="text-uppercase">{{ $student->name }}</strong>
                                </div>
                                <div class="clearfix"></div>
                                <div class="col-md-12  text-left pad5">
                                    <span class="text-bold">ID NO : </span> {{ $student->std_code }} <span
                                        class="text-bold"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Gender : </span> {{ $student->gender }}
                                    <br/>
                                    @if($parent)
                                        <span class="text-bold">Father's Name: </span>{{ $parent->father_name }}<br/>
                                        <span class="text-bold">Mother's Name: </span>{{ $parent->mother_name }}<br/>
                                    @endif
                                    <span class="text-bold">Date of Birth :</span> {{ $student->dob }}<br/>
                                </div>
                                <div class="clearfix"></div>
                                <div class="col-md-12 signature pull-right" style="margin-top: -10px;">
                                    <p class="text-right">
                                        <img style="border: none;" src="{{ asset('assets/images/head_master.png') }}"
                                             class="img-responsive img-thumbnail"
                                             width="80px"/><br/>
                                        ==================<br/>
                                        Head Master
                                    </p>
                                </div>
                                <div class="clearfix"></div>
                                <hr/>
                                <div class="col-md-12  text-center">
                                    <p>{{ $app_settings->website }}</p>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <button type='button' id='btn' class='btn btn-success pull-right' value='Print'
                                onClick='printContent();'>Print ID Card
                        </button>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <!-- /.tab-content -->
        </div>
        <!-- nav-tabs-custom -->
    </div>
</div>
<style>
    .card {
        border: 3px solid #f6f6f6;
    }

    .card .card_logo {
        text-align: center;
        display: block;
        margin: 0 auto;
    }

    .std_img {
        text-align: center;
        display: block;
        margin: 0 auto;
    }

    .pad5 {
        padding: 10px;
    }
</style>
<script>
    function printContent() {
        $('.card').printThis({
            importCSS: true,
            importStyle: true,//thrown in for extra measure
            loadCSS: "{{ asset('/assets/css/id_card.css') }}",

        });
    }
</script>

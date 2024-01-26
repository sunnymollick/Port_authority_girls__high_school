<div class="row">
    <div class="col-md-12">
        <!-- Custom Tabs -->
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#book" data-toggle="tab" aria-expanded="true">Books Information</a>
                </li>
                <li class=""><a href="#request" data-toggle="tab" aria-expanded="false">Request Information</a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="book">
                    <div class="col-md-8 col-sm-12 table-responsive">
                        <table id="view_details" class="table table-bordered table-hover">
                            <tbody>
                            <tr>
                                <td class="subject"> Book's Name</td>
                                <td> :</td>
                                <td> {{ $bookrequest->book ? $bookrequest->book->name : 'Book Not Found' }}</td>
                            </tr>
                            <tr>
                                <td class="subject"> Issued Start Date</td>
                                <td> :</td>
                                <td> {{ $bookrequest->issue_start_date }} </td>
                            </tr>
                            <tr>
                                <td class="subject"> Issued End Date</td>
                                <td> :</td>
                                <td> {{ $bookrequest->issue_end_date }} </td>
                            </tr>
                            <tr>
                                <td class="subject"> Returned Date</td>
                                <td> :</td>
                                <td> {{ $bookrequest->returned_date }} </td>
                            </tr>
                            <tr>
                                <td class="subject"> Status</td>
                                <td> :</td>
                                <td> @php $status = $bookrequest->status ? '<span class="label label-success">Returned</span>' : '<span class="label label-danger">Issued</span>' ;  @endphp {!! $status !!}   </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-4 col-sm-12 short_inf">
                        @if($bookrequest->book)
                            <img src="{{ asset($bookrequest->book->file_path) }}" class="img-responsive img-thumbnail"
                                 width="200px"/><br/><br/>
                            Book's Name : {{ $bookrequest->book->name  }} <br/>
                            Book's Author : {{ $bookrequest->book->author  }} <br/>
                        @endif
                    </div>
                </div>
                <!-- /.tab-pane -->
                <div class="tab-pane" id="request">
                    @if($student)
                        <div class="col-md-8 col-sm-12 table-responsive">
                            <table id="view_details" class="table table-bordered table-hover">
                                <tbody>
                                <tr>
                                    <td class="subject"> Student's Name</td>
                                    <td> :</td>
                                    <td> {{ $student->name }} </td>
                                </tr>
                                <tr>
                                    <td class="subject"> Student's Code</td>
                                    <td> :</td>
                                    <td> {{ $student->std_code }} </td>
                                </tr>
                                <tr>
                                    <td class="subject"> Class</td>
                                    <td> :</td>
                                    <td> {{ $enroll->stdclass ? $enroll->stdclass->name : '' }} </td>
                                </tr>
                                <tr>
                                    <td class="subject"> Section</td>
                                    <td> :</td>
                                    <td>{{ $enroll->section ? $enroll->section->name : '' }}</td>
                                </tr>
                                <tr>
                                    <td class="subject"> Roll</td>
                                    <td> :</td>
                                    <td>{{ $enroll ? $enroll->roll : '' }} </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-4 col-sm-12">
                            <img src="{{ asset($student->file_path) }}" class="img-responsive img-thumbnail"
                                 width="200px"/><br/><br/>
                            Name : {{ $student->name  }} <br/>
                            Email : {{ $student->email  }} <br/>
                            Phone : {{ $student->phone  }} <br/>
                        </div>
                    @endif
                </div>
                <!-- /.tab-pane -->
                <div class="clearfix"></div>
            </div>
            <!-- /.tab-content -->
        </div>
        <!-- nav-tabs-custom -->
    </div>
</div>

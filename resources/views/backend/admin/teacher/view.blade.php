<div class="row">
    <div class="col-md-9 col-sm-12 table-responsive">
        <table id="view_details" class="table table-bordered table-hover">
            <tbody>
            <tr>
                <td class="subject"> Name</td>
                <td> :</td>
                <td> {{ $teacher->name }} </td>
            </tr>
            <tr>
                <td class="subject"> Gender</td>
                <td> :</td>
                <td> {{ $teacher->gender }} </td>
            </tr>
            <tr>
                <td class="subject"> Birth Date</td>
                <td> :</td>
                <td> {{ $teacher->dob }} </td>
            </tr>
            <tr>
                <td class="subject"> Religion</td>
                <td> :</td>
                <td> {{ $teacher->religion }} </td>
            </tr>
            <tr>
                <td class="subject"> School Join Date</td>
                <td> :</td>
                <td> {{ $teacher->doj }} </td>
            </tr>
            <tr>
                <td class="subject"> Qualification</td>
                <td> :</td>
                <td> {{ $teacher->qualification }} </td>
            </tr>
            <tr>
                <td class="subject"> Subject</td>
                <td> :</td>
                <td> {{ $teacher->subject }} </td>
            </tr>
            <tr>
                <td class="subject"> Marital Status</td>
                <td> :</td>
                <td> {{ $teacher->marital_status }} </td>
            </tr>
            <tr>
                <td class="subject"> Address</td>
                <td> :</td>
                <td> {{ $teacher->address }} </td>
            </tr>
            <tr>
                <td class="subject"> Status</td>
                <td> :</td>
                <td> @php $status = $teacher->status ? '<span class="label label-success">Active</span>' : '<span class="label label-danger">Inactive</span>' ;  @endphp {!! $status !!}   </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="col-md-3 col-sm-12">
        <img src="{{ asset($teacher->file_path) }}" class="img-responsive img-thumbnail" width="200px"/><br/><br/>
        Teacher ID : {{ $teacher->teacher_code  }} <br/>
        Email : {{ $teacher->email  }} <br/>
        Phone : {{ $teacher->phone  }} <br/>
        Designation : {{ $teacher->designation  }} <br/>
    </div>
</div>
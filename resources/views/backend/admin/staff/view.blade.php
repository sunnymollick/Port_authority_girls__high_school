<div class="row">
    <div class="col-md-9 col-sm-12 table-responsive">
        <table id="view_details" class="table table-bordered table-hover">
            <tbody>
            <tr>
                <td class="subject"> Name</td>
                <td> :</td>
                <td> {{ $staff->name }} </td>
            </tr>
            <tr>
                <td class="subject"> Qualification</td>
                <td> :</td>
                <td> {{ $staff->qualification }} </td>
            </tr>
            <tr>
                <td class="subject"> Gender</td>
                <td> :</td>
                <td> {{ $staff->gender }} </td>
            </tr>
            <tr>
                <td class="subject"> Religion</td>
                <td> :</td>
                <td> {{ $staff->religion }} </td>
            </tr>
            <tr>
                <td class="subject"> School Join Date</td>
                <td> :</td>
                <td> {{ $staff->doj }} </td>
            </tr>
            <tr>
                <td class="subject"> Address</td>
                <td> :</td>
                <td> {{ $staff->address }} </td>
            </tr>
            <tr>
                <td class="subject"> Status</td>
                <td> :</td>
                <td> @php $status = $staff->status ? '<span class="label label-success">Active</span>' : '<span class="label label-danger">Inactive</span>' ;  @endphp {!! $status !!}   </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="col-md-3 col-sm-12">
        <img src="{{ asset($staff->file_path) }}" class="img-responsive img-thumbnail" width="200px"/><br/><br/>
        Email : {{ $staff->email  }} <br/>
        Phone : {{ $staff->phone  }} <br/>
        Designation : {{ $staff->designation  }} <br/>
    </div>
</div>
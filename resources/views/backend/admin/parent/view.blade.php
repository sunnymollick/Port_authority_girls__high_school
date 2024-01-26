<div class="row">
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
        <img src="{{ asset($parent->file_path) }}" class="img-responsive img-thumbnail" width="200px"/><br/><br/>
        Name : {{ $parent->name  }} <br/>
        Email : {{ $parent->email  }} <br/>
        Phone : {{ $parent->phone  }} <br/>
    </div>
</div>
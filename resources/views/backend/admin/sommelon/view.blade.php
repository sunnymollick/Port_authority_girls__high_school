<div class="row">
    <div class="col-md-8 col-sm-12 table-responsive">
        <table id="view_details" class="table table-bordered table-hover">
            <tbody>
                <tr>
                <td class="subject"> Applicant's Photo</td>
                <td> :</td>
                <td>
                    @if($sommelon->file_path)
                        <img src="{{asset($sommelon->file_path)}}" width="60px">
                    @else
                        No photo uploaded
                    @endif
                </td>
            </tr>
            <tr>
                <td class="subject"> Applicant's Serial</td>
                <td> :</td>
                <td> {{ $sommelon->sl }} </td>
            </tr>
            <tr>
                <td class="subject"> Registration Date</td>
                <td> :</td>
                <td> {{ $sommelon->reg_date }} </td>
            </tr>
            <tr>
                <td class="subject"> Applicant's Name</td>
                <td> :</td>
                <td> {{ $sommelon->std_name }} </td>
            </tr>
            <tr>
                <td class="subject"> SSC Batch</td>
                <td> :</td>
                <td> {{ $sommelon->ssc_batch }} </td>
            </tr>
            <tr>
                <td class="subject"> Father's Name</td>
                <td> :</td>
                <td> {{ $sommelon->std_father_name }} </td>
            </tr>
            <tr>
                <td class="subject"> Mother's Name</td>
                <td> :</td>
                <td> {{ $sommelon->std_mother_name }} </td>
            </tr>
            <tr>
                <td class="subject"> Date of Birth</td>
                <td> :</td>
                <td> {{ $sommelon->std_dob }} </td>
            </tr>

            <tr>
                <td class="subject"> Blood Group</td>
                <td> :</td>
                <td> {{ $sommelon->blood_group }} </td>
            </tr>
            <tr>
                <td class="subject"> Present Address</td>
                <td> :</td>
                <td> {{ $sommelon->prs_address }} </td>
            </tr>
            <tr>
                <td class="subject"> Parmanent Address</td>
                <td> :</td>
                <td> {{ $sommelon->prm_address }} </td>
            </tr>
            <tr>
                <td class="subject"> Mobile</td>
                <td> :</td>
                <td> {{ $sommelon->mobile }} </td>
            </tr>
            <tr>
                <td class="subject"> Email</td>
                <td> :</td>
                <td> {{ $sommelon->email }} </td>
            </tr>
            <tr>
                <td class="subject"> Educational Qualification</td>
                <td> :</td>
                <td> {{ $sommelon->education }} </td>
            </tr>
            <tr>
                <td class="subject"> Qualification Year</td>
                <td> :</td>
                <td> {{ $sommelon->session }} </td>
            </tr>
            <tr>
                <td class="subject"> University Name</td>
                <td> :</td>
                <td> {{ $sommelon->university_name }} </td>
            </tr>
            <tr>
                <td class="subject"> Current Profession</td>
                <td> :</td>
                <td> {{ $sommelon->profession }} </td>
            </tr>
            <tr>
                <td class="subject"> Designation & Current working place</td>
                <td> :</td>
                <td> {{ $sommelon->designation_work_place }} </td>
            </tr>
            <tr>
                <td class="subject"> Bikash Transaction ID</td>
                <td> :</td>
                <td> {{ $sommelon->bikash_trans_id }} </td>
            </tr>
            <tr>
                <td class="subject"> Bikash Transaction Date</td>
                <td> :</td>
                <td> {{ $sommelon->bikash_trans_date }} </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
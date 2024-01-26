<div class="row" id="application">
    <div class="col-md-12" id="heading">
        <div class="col-md-10 text-center" id="logo_section">
            <img id="logo_img" src="{{ asset($app_settings->logo) }}" width="30%"/><br/>
            <strong>EIIN : 138212</strong><br/>
            <strong>Phone : +88 031-62775</strong><br/>
            <h6>Application Form</h6>
            =============================
        </div>
        <div class="col-md-2" id="passport_img_section">
            <img id="applicant_img" src="{{ asset($admissionApplication->file_path) }}" class="img-thumbnail" width="70%"/>
        </div>
    </div>
    <div class="col-md-12">
        <table class="table table-hover table-bordered">
            <tr>
                <td>Applicant's Name</td>
                <td>:</td>
                <td>In English : {{$admissionApplication->applicant_name_en}},
                    In Bangla : {{$admissionApplication->applicant_name_bn}}
                </td>
            </tr>
            <tr>
                <td>Father's Name</td>
                <td>:</td>
                <td>In English : {{$admissionApplication->father_name_en}},
                    In Bangla : {{$admissionApplication->father_name_bn}}
                </td>
            </tr>
            <tr>
                <td>Mother's Name</td>
                <td>:</td>
                <td>In English : {{$admissionApplication->mother_name_en}},
                    In Bangla : {{$admissionApplication->mother_name_bn}}
                </td>
            </tr>
            <tr>
                <td>Father's Qualification</td>
                <td>:</td>
                <td> {{$admissionApplication->father_qualification}}
                </td>
            </tr>
            <tr>
                <td>Mother's Qualification</td>
                <td>:</td>
                <td> {{$admissionApplication->mother_qualification}}
                </td>
            </tr>
            <tr>
                <td>Father's Occupation</td>
                <td>:</td>
                <td> {{$admissionApplication->father_occupation}}
                </td>
            </tr>
            <tr>
                <td>Mother's Occupation</td>
                <td>:</td>
                <td> {{$admissionApplication->mother_occupation}}
                </td>
            </tr>
            <tr>
                <td>Yearly Income</td>
                <td>:</td>
                <td> {{$admissionApplication->yearly_income}}
                </td>
            </tr>
            <tr>
                <td>Father's Occupation Post Name</td>
                <td>:</td>
                <td> {{$admissionApplication->father_occupation_post_name}}
                </td>
            </tr>
            <tr>
                <td>Father's Occupation Organization Name</td>
                <td>:</td>
                <td> {{$admissionApplication->father_occupation_org_name}}
                </td>
            </tr>
            <tr>
                <td>Father's Business Type</td>
                <td>:</td>
                <td> {{$admissionApplication->father_occupation_business_type}}
                </td>
            </tr>
            <tr>
                <td>Other Gurdian Information</td>
                <td>:</td>
                <td>
                    {{' Name : ' . $admissionApplication->alternet_gurdian_name .
                    ' , Phone : ' .  $admissionApplication->alternet_gurdian_phone . ' , Address : ' .  $admissionApplication->alternet_gurdian_address}}
                </td>
            </tr>
            <tr>
                <td>Date of Birth</td>
                <td>:</td>
                <td> {{$admissionApplication->dob}}
                </td>
            </tr>
            <tr>
                <td>Mobile</td>
                <td>:</td>
                <td> {{$admissionApplication->mobile}}
                </td>
            </tr>
            <tr>
                <td>Email</td>
                <td>:</td>
                <td> {{$admissionApplication->email}}
                </td>
            </tr>
            <tr>
                <td>Present Address Information</td>
                <td>:</td>
                <td> {{ ' Village : ' . $admissionApplication->present_village . ', Post Office : ' . $admissionApplication->present_post_office .
                ', Thana : ' . $admissionApplication->present_thana . ', District : ' . $admissionApplication->present_district}}
                </td>
            </tr>
            <tr>
                <td>Parmanent Address Information</td>
                <td>:</td>
                <td> {{ ' Village : ' . $admissionApplication->parmanent_village . ', Post Office : ' . $admissionApplication->parmanent_post_office .
                ', Thana : ' . $admissionApplication->parmanent_thana . ', District : ' . $admissionApplication->parmanent_district}}
                </td>
            </tr>
            <tr>
                <td>Nationality</td>
                <td>:</td>
                <td> {{$admissionApplication->nationality}}
                </td>
            </tr>
            <tr>
                <td>Religion</td>
                <td>:</td>
                <td> {{$admissionApplication->religion}}
                </td>
            </tr>
            <tr>
                <td>Children already Read in School</td>
                <td>:</td>
                <td> {{$admissionApplication->children_in_school . ' Name : '
                 . $admissionApplication->children_name . ', District : ' . $admissionApplication->children_class .
                  ', District : ' . $admissionApplication->children_section}}
                </td>
            </tr>
            <tr>
                <td>Wnat to admit class</td>
                <td>:</td>
                <td> {{'Class Name : ' . $admissionApplication->stdclass->name . ', Section : ' . $admissionApplication->section->name}}
                </td>
            </tr>
            <tr>
                <td>Previous School</td>
                <td>:</td>
                <td> {{'Old School Name : ' . $admissionApplication->old_school_name . ', Old Class : ' . $admissionApplication->old_class}}
                </td>
            </tr>
        </table>
    </div>
</div>
<div class="clearfix"></div>
<div class="col-md-12">
    <button type='button' id='btn' class='btn btn-success pull-right' value='Print'
            onClick='printContent();'>Print
    </button>
</div>
<div class="clearfix"></div>
<style>
    #application table td {
        font-size: 13px;
        line-height: 25px;
    }
</style>
<script>
    function printContent() {
        $('#application').printThis({
            importCSS: true,
            importStyle: true,//thrown in for extra measure
            loadCSS: "{{ asset('/assets/css/admission_details.css') }}",

        });
    }
</script>
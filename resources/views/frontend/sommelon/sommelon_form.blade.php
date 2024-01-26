<!DOCTYPE html>
<html>
<head>
    @include('frontend.layouts.head')
</head>
<body>
<div class="container">
    <!-- Page Preloder -->
    <div id="preloder">
        <div class="loader"></div>
    </div>
    <!-- header section -->
    <header class="header-section">
        @include('frontend.layouts.header')
    </header>
    <div class="jumbotron" style="background: #eaffff; padding: 30px">
        <div class="row">
            <div class="col-md-2">
                <img src="{{asset('assets/images/Port-girls-school.png')}}" width="160px">
            </div>
            <div class="col-md-8">
                <div class="section-title text-center">
                    <h3 style="text-transform: uppercase; font-weight: 700">Former/Current Students Alumni</h3>
                    <h4 style="text-transform: uppercase; font-weight: 500">Chittagong Port Authority Girls High
                        School</h4>
                    <h4 style="text-transform: uppercase;font-weight: 400; margin-top: 10px">Registration Form</h4>
                </div>
            </div>
            <div class="col-md-2">
                <img src="{{asset('assets/images/passport.png')}}" width="140px">
            </div>
        </div>
        <div class="row">
            <br/>
            <div id="status"></div>
            <br/>
            <div class="col-md-12"><br/>
                <h5 style="line-height: 25px;font-family: 'PT Sans';"> I am a former/current student of Chittagong Port
                    Authority Girls High School.
                    I would like to list my name by promising to abide by the rules of this conference.
                </h5>
            </div>
        </div>
        <div class="row">
            <br/><br/>
            <form id='create' enctype="multipart/form-data" method="post" accept-charset="utf-8">
                <div class="form-group col-md-4 col-sm-12">
                    <label for=""> Serial Number </label>
                    <input type="text" class="form-control" id="sl" name="sl" value="{{$serial}}" readonly>
                </div>
                <div class="form-group col-md-4 col-sm-12">
                    <label for=""> Registration No. </label>
                    <input type="text" class="form-control" id="reg_no" name="reg_no" value="" readonly>
                </div>
                <div class="form-group col-md-4 col-sm-12">
                    <label for=""> Registration Date</label>
                    <input type="text" class="form-control" id="reg_date" name="reg_date" value="{{date('Y-m-d')}}"
                           readonly>
                </div>
                <div class="clearfix"></div>
                <div class="form-group col-md-8 col-sm-12">
                    <label for=""> Applicant Name * </label>
                    <input type="text" class="form-control" id="std_name" name="std_name" value=""
                           required>
                </div>
                <div class="form-group col-md-4 col-sm-12">
                    <label for=""> Batch </label>
                    <input type="text" class="form-control" id="ssc_batch" name="ssc_batch" value=""
                           required>
                </div>
                <div class="clearfix"></div>
                <div class="form-group col-md-4 col-sm-12">
                    <label for=""> Father's Name * </label>
                    <input type="text" class="form-control" id="std_father_name" name="std_father_name" value=""
                           required>
                </div>
                <div class="form-group col-md-4 col-sm-12">
                    <label for=""> Mother's Name * </label>
                    <input type="text" class="form-control" id="std_mother_name" name="std_mother_name" value=""
                           required>
                </div>
                <div class="form-group col-md-2 col-sm-12">
                    <label for=""> Blood Group </label>
                    <input type="text" class="form-control" id="blood_group"
                           name="blood_group" value="">
                    <span id="error_blood_group" class="has-error"></span>
                </div>
                <div class="form-group col-md-2 col-sm-12">
                    <label for=""> Date of Birth </label>
                    <input type="text" class="form-control" id="std_dob"
                           name="std_dob" value="" required readonly>
                    <span id="error_std_dob" class="has-error"></span>
                </div>
                <div class="clearfix"></div>
                <div class="form-group col-md-6 col-sm-12">
                    <label for=""> Present Address *</label>
                    <input type="text" class="form-control" id="prs_address"
                           name="prs_address" value="" required>
                </div>
                <div class="form-group col-md-6 col-sm-12">
                    <label for=""> Parmanent Address </label>
                    <input type="text" class="form-control" id="prm_address"
                           name="prm_address" value="">
                </div>
                <div class="clearfix"></div>
                <div class="form-group col-md-3 col-sm-12">
                    <label for=""> Mobile * </label>
                    <input type="text" class="form-control" id="mobile"
                           name="mobile" value="" required>
                    <span id="error_mobile" class="has-error"></span>
                </div>
                <div class="form-group col-md-3 col-sm-12">
                    <label for=""> Email </label>
                    <input type="text" class="form-control" id="email"
                           name="email" value="">
                    <span id="error_email" class="has-error"></span>
                </div>
                <div class="form-group col-md-4 col-sm-12">
                    <label for=""> Educational Qualification </label>
                    <input type="text" class="form-control" id="education"
                           name="education" value="">
                </div>
                <div class="form-group col-md-2 col-sm-12">
                    <label for=""> Qualification Year </label>
                    <input type="text" class="form-control" id="session"
                           name="session" value="">
                </div>
                <div class="clearfix"></div>
                <div class="form-group col-md-4 col-sm-12">
                    <label for=""> University/Institute Name </label>
                    <input type="text" class="form-control" id="university_name"
                           name="university_name" value="">
                </div>
                <div class="form-group col-md-3 col-sm-12">
                    <label for=""> Profession </label>
                    <input type="text" class="form-control" id="profession"
                           name="profession" value="">
                </div>
                <div class="form-group col-md-5 col-sm-12">
                    <label for=""> Designation & Current working place </label>
                    <input type="text" class="form-control" id="designation_work_place"
                           name="designation_work_place" value="">
                </div>
                <div class="clearfix"></div>
                <div class="col-md-12">
                    <strong class="note">Note* : All the student have requested to sent 620 TK (Six Hundred and Twenty Tk) by bikash.Bikash Number personal (01711880416). After sending money please fill bikash
                        transaction id and transaction date here. <br/></strong><br/>
                </div>
                <div class="form-group col-md-6 col-sm-12">
                    <label for=""> Bikash Transaction Id * </label>
                    <input type="text" class="form-control" id="bikash_trans_id"
                           name="bikash_trans_id" value="" required>
                </div>
                <div class="form-group col-md-6 col-sm-12">
                    <label for=""> Bikash Transaction Date * </label>
                    <input type="text" class="form-control" id="bikash_trans_date"
                           name="bikash_trans_date" value="" required readonly>
                </div>
                <div class="clearfix"></div>
                <div class="form-group col-md-6">
                    <label for="photo"> Upload your photo </label>
                    <input id="photo" type="file" name="photo" style="display:none">
                    <div class="input-group">
                        <div class="input-group-btn">
                            <a class="btn btn-success form-control" onclick="$('input[id=photo]').click();">Browse</a>
                        </div><!-- /btn-group -->
                        <input type="text" name="Selectedphoto" class="form-control" id="Selectedphoto"
                               value="" readonly>
                    </div>
                    <script type="text/javascript">
                        $('input[id=photo]').change(function () {
                            $('#Selectedphoto').val($(this).val());
                        });
                    </script>
                    <label class="help_block">Image must be jpeg, jpg or png format</label>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-12">
                    <br/>
                    <strong class="note">Note* : Please attach 3 passport size images. Write
                        name and passed year in the opposite side of the images.</strong><br/><br/>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-12">
                    <button class="site-btn submit">SUBMIT</button>
                    <div id="loader"> <img src="{{asset('assets/images/loadingg.gif')}}" width="20px"> Please wait ...</div>
                </div>
            </form>
        </div>
    </div>
    <!-- Footer section -->
    <footer class="footer-section">
        @include('frontend.layouts.footer')
    </footer>
</div>
<style>
    .note {
        line-height: 25px;
        color: #e53f45;
        font-family: 'PT Sans';
        font-size: 15px;
    }
</style>
<script>
    $(document).ready(function () {

        $('#loader').hide();
        $('#std_dob').datepicker({format: "yyyy-mm-dd"}).on('changeDate', function (e) {
            $(this).datepicker('hide');
        });
        $('#bikash_trans_date').datepicker({format: "yyyy-mm-dd"}).on('changeDate', function (e) {
            $(this).datepicker('hide');
        });

        $('#create').validate({// <- attach '.validate()' to your form
            // Rules for form validation
            rules: {
                name: {
                    required: true
                }
            },
            messages: {
                name: {
                    required: 'Enter your name'
                }
            },
            submitHandler: function (form) {

                var myData = new FormData($("#create")[0]);
                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                myData.append('_token', CSRF_TOKEN);

                $.ajax({
                    url: 'sommelon',
                    type: 'POST',
                    data: myData,
                    dataType: 'json',
                    cache: false,
                    processData: false,
                    contentType: false,
                    beforeSend: function () {
                        $('#loader').show();
                        $(".submit").prop('disabled', true); // disable button
                    },
                    success: function (data) {
                        if (data.type === 'success') {
                            $('#loader').hide();
                            $(".submit").prop('disabled', false); // disable button
                            $("html, body").animate({scrollTop: 0}, "slow");
                            $('#status').html(data.message); // hide bootstrap modal
                            document.getElementById("create").reset();

                        } else if (data.type === 'error') {
                            if (data.errors) {
                                $.each(data.errors, function (key, val) {
                                    $('#error_' + key).html(val);
                                });
                            }
                            $("#status").html(data.message);
                            $('#loader').hide();
                            $(".submit").prop('disabled', false); // disable button

                        }
                    }
                });
            }
            // <- end 'submitHandler' callback
        });                    // <- end '.validate()'

    });
</script>
</body>
</html>



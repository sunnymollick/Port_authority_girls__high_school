<form id='edit' action="" enctype="multipart/form-data" method="post" accept-charset="utf-8">
    <div id="status"></div>
    {{method_field('PATCH')}}
    <div class="form-group col-md-6 col-sm-12">
        <label for=""> Book's Name </label>
        <select name="book_id" class="form-control filter" required>
            @foreach($books as $book)
                <option value="{{$book->id}}"
                    {{ ( $book->id == $bookrequest->book_id) ? 'selected' : '' }} >{{$bookrequest->book->name}}</option>
            @endforeach
        </select>
        <span id="error_book_id" class="has-error"></span>
    </div>
    <div class="form-group col-md-6 col-sm-12">
        <label for=""> Student's Id </label>
        <input type="text" class="form-control" id="student_code" name="student_code" value="{{$bookrequest->student_code}}"
               placeholder="" required>
        <span id="error_student_code" class="has-error"></span>
    </div>
    <div class="clearfix"></div>
    <div class="form-group col-md-4 col-sm-12">
        <label for=""> Issue Start Date </label>
        <input type="text" class="form-control" id="issue_start_date" name="issue_start_date"
               value="{{$bookrequest->issue_start_date}}" required/>
        <span id="error_issue_start_date" class="has-error"></span>
    </div>
    <div class="form-group col-md-4 col-sm-12">
        <label for=""> Issue End Date </label>
        <input type="text" class="form-control" id="issue_end_date" name="issue_end_date"
               value="{{$bookrequest->issue_end_date}}" required/>
        <span id="error_issue_end_date" class="has-error"></span>
    </div>
    <div class="form-group col-md-4 col-sm-12">
        <label for=""> Returned Date </label>
        <input type="text" class="form-control" id="returned_date" name="returned_date"
               value="{{$bookrequest->returned_date}}"/>
        <span id="error_returned_date" class="has-error"></span>
    </div>
    <div class="form-group col-md-6">
        <label for=""> Status </label><br/>
        <input type="radio" name="status" class="flat-green"
               value="1" {{ ( $bookrequest->status == 1 ) ? 'checked' : '' }} /> Returned
        <input type="radio" name="status" class="flat-green"
               value="0" {{ ( $bookrequest->status == 0 ) ? 'checked' : '' }}/> Issued
    </div>
    <div class="clearfix"></div>
    <div class="form-group col-md-12">
        <button type="submit" class="btn btn-success submit"><span class="fa fa-save fa-fw"></span> Save</button>
        <button type="button" class="btn btn-default" data-dismiss="modal"><span
                class="fa fa-times-circle fa-fw"></span> Cancel
        </button>
    </div>
    <div class="clearfix"></div>
</form>
<script>
    $('#issue_start_date').datepicker({format: "yyyy-mm-dd"}).on('changeDate', function (e) {
        $(this).datepicker('hide');
    });
    $('#issue_end_date').datepicker({format: "yyyy-mm-dd"}).on('changeDate', function (e) {
        $(this).datepicker('hide');
    });
    $('#returned_date').datepicker({format: "yyyy-mm-dd"}).on('changeDate', function (e) {
        $(this).datepicker('hide');
    });
    $('input[type="radio"].flat-green').iCheck({
        checkboxClass: 'icheckbox_flat-green',
        radioClass: 'iradio_flat-green'
    });
    $(document).ready(function () {
        $('#loader').hide();
        $('#edit').validate({// <- attach '.validate()' to your form
            // Rules for form validation
            rules: {
                name: {
                    required: true
                },
                in_digit: {
                    required: true,
                    number: true
                }
            },
            // Messages for form validation
            messages: {
                name: {
                    required: 'Enter Book name'
                }
            },
            submitHandler: function (form) {

                var myData = new FormData($("#edit")[0]);
                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                myData.append('_token', CSRF_TOKEN);

                $.ajax({
                    url: 'bookrequests/' + '{{ $bookrequest->id }}',
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
                            reload_table();
                            notify_view(data.type, data.message);
                            $('#loader').hide();
                            $(".submit").prop('disabled', false); // disable button
                            $("html, body").animate({scrollTop: 0}, "slow");
                            $('#myModal').modal('hide'); // hide bootstrap modal

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
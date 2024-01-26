<form id='create' action="" enctype="multipart/form-data" method="post" accept-charset="utf-8">
    <div id="status"></div>
    <div class="form-group col-md-6 col-sm-12">
        <label for=""> Grade Name </label>
        <input type="text" class="form-control" id="name" name="name" value=""
               placeholder="" required>
        <span id="error_name" class="has-error"></span>
    </div>
    <div class="form-group col-md-6 col-sm-12">
        <label for=""> Grade Point </label>
        <input type="text" class="form-control" id="grade_point" name="grade_point" value=""
               placeholder="" required>
        <span id="error_grade_point" class="has-error"></span>
    </div>
    <div class="clearfix"></div>
    <div class="form-group col-md-6 col-sm-12">
        <label for=""> Mark's From </label>
        <input type="text" class="form-control" id="mark_from" name="mark_from" value=""
               placeholder="" min="0" required>
        <span id="error_mark_from" class="has-error"></span>
    </div>
    <div class="form-group col-md-6 col-sm-12">
        <label for=""> Mark's Upto </label>
        <input type="text" class="form-control" id="mark_upto" name="mark_upto" value=""
               placeholder="" min="0" required>
        <span id="error_mark_upto" class="has-error"></span>
    </div>
    <div class="clearfix"></div>
    <div class="form-group col-md-12 col-sm-12">
        <label for=""> Comment </label>
        <input type="text" class="form-control" id="comment" name="comment" value=""
               placeholder="">
        <span id="error_comment" class="has-error"></span>
    </div>
    <div class="form-group col-md-12">
        <button type="submit" class="btn btn-success button-submit"
                data-loading-text="Loading..."><span class="fa fa-save fa-fw"></span> Save
        </button>
        <button type="button" class="btn btn-default" data-dismiss="modal"><span
                class="fa fa-times-circle fa-fw"></span> Cancel
        </button>
    </div>
    <div class="clearfix"></div>
</form>


<script>
    $(document).ready(function () {

        $('#loader').hide();

        $('#create').validate({// <- attach '.validate()' to your form
            // Rules for form validation
            rules: {
                name: {
                    required: true
                },
                mark_from: {
                    required: true,
                    number: true
                },
                mark_upto: {
                    required: true,
                    number: true
                }
            },
            // Messages for form validation
            messages: {
                name: {
                    required: 'Enter grade name'
                }
            },
            submitHandler: function (form) {

                var myData = new FormData($("#create")[0]);
                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                myData.append('_token', CSRF_TOKEN);

                $.ajax({
                    url: 'grades',
                    type: 'POST',
                    data: myData,
                    dataType: 'json',
                    cache: false,
                    processData: false,
                    contentType: false,
                    beforeSend: function () {
                        $('#loader').show();
                        $(".button-submit").prop('disabled', true); // disable button
                    },
                    success: function (data) {
                        if (data.type === 'success') {
                            reload_table();
                            notify_view(data.type, data.message);
                            $('#loader').hide();
                            $(".button-submit").prop('disabled', false); // disable button
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
                            $(".button-submit").prop('disabled', false); // disable button

                        }
                    }
                });
            }
            // <- end 'submitHandler' callback
        });                    // <- end '.validate()'

    });
</script>
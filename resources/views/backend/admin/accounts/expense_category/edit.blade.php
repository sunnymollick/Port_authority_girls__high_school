<form id='edit' action="" enctype="multipart/form-data" method="post" accept-charset="utf-8">
    <div id="status"></div>
    {{method_field('PATCH')}}
    <div class="form-group col-md-12 col-sm-12">
        <label for=""> Category Name </label>
        <input type="text" class="form-control" id="name" name="name" value="{{ $expensecategory->name }}"
               placeholder="" required>
        <span id="error_name" class="has-error"></span>
    </div>
    <div class="form-group col-md-12">
        <label for=""> Status </label><br/>
        <input type="radio" name="status" class="flat-green"
               value="1" {{ ( $expensecategory->status == 1 ) ? 'checked' : '' }} /> Active
        <input type="radio" name="status" class="flat-green"
               value="0" {{ ( $expensecategory->status == 0 ) ? 'checked' : '' }}/> In Active
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
    $(document).ready(function () {

        $('input[type="radio"].flat-green').iCheck({
            checkboxClass: 'icheckbox_flat-green',
            radioClass: 'iradio_flat-green'
        });
        $('#loader').hide();
        $('#edit').validate({// <- attach '.validate()' to your form
            // Rules for form validation
            rules: {
                name: {
                    required: true
                }
            },
            // Messages for form validation
            messages: {
                name: {
                    required: 'Enter class category name'
                }
            },
            submitHandler: function (form) {

                var myData = new FormData($("#edit")[0]);
                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                myData.append('_token', CSRF_TOKEN);

                $.ajax({
                    url: 'expensecategory/' + '{{ $expensecategory->id }}',
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
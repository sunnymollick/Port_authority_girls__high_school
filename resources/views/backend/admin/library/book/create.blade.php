<form id='create' action="" enctype="multipart/form-data" method="post" accept-charset="utf-8">
    <div id="status"></div>
    <div class="form-group col-md-6 col-sm-12">
        <label for=""> Book's Name </label>
        <input type="text" class="form-control" id="name" name="name" value=""
               placeholder="" required>
        <span id="error_name" class="has-error"></span>
    </div>
    <div class="form-group col-md-2 col-sm-12">
        <label for=""> Class </label>
        <select name="class_id" class="form-control" required>
            @foreach($stdclass as $class)
                <option value="{{$class->id}}">{{$class->name}}</option>
            @endforeach
        </select>
        <span id="error_class_id" class="has-error"></span>
    </div>
    <div class="form-group col-md-4 col-sm-12">
        <label for=""> Author's Name </label>
        <input type="text" class="form-control" id="author" name="author" value=""
               placeholder="">
        <span id="error_author" class="has-error"></span>
    </div>
    <div class="clearfix"></div>
    <div class="form-group col-md-6 col-sm-12">
        <label for=""> Description </label>
        <input type="text" class="form-control" id="description" name="description" value=""
               placeholder="">
        <span id="error_description" class="has-error"></span>
    </div>
    <div class="form-group col-md-3 col-sm-12">
        <label for=""> Book's Price </label>
        <input type="text" class="form-control" id="price" name="price" value=""
               placeholder="">
        <span id="error_price" class="has-error"></span>
    </div>
    <div class="form-group col-md-3 col-sm-12">
        <label for="total_copies"> Total Copies </label>
        <input type="number" class="form-control" id="total_copies" name="total_copies" min="0" value=""
               placeholder="">
        <span id="error_total_copies" class="has-error"></span>
    </div>
    <div class="clearfix"></div>
    <div class="col-md-12">
        <label for="photo">Upload Image</label>
        <input id="photo" type="file" name="photo" style="display:none">
        <div class="input-group">
            <div class="input-group-btn">
                <a class="btn btn-success" onclick="$('input[id=photo]').click();">Browse</a>
            </div><!-- /btn-group -->
            <input type="text" name="SelectedFileName" class="form-control" id="SelectedFileName"
                   value="" readonly>
        </div>
        <div class="clearfix"></div>
        <p class="help-block">File must be jpg, jpeg, png.</p>
        <script type="text/javascript">
            $('input[id=photo]').change(function () {
                $('#SelectedFileName').val($(this).val());
            });
        </script>
        <span id="error_photo" class="has-error"></span>
    </div>
    <div class="clearfix"></div>
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
                total_copies: {
                    required: true,
                    number: true
                }
            },
            // Messages for form validation
            messages: {
                name: {
                    required: 'Enter book name'
                }
            },
            submitHandler: function (form) {

                var myData = new FormData($("#create")[0]);
                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                myData.append('_token', CSRF_TOKEN);

                $.ajax({
                    url: 'books',
                    type: 'POST',
                    data: myData,
                    dataType: 'json',
                    cache: false,
                    processData: false,
                    contentType: false,
                    beforeSend: function () {
                        $('#loader').show();
                        $("#submit").prop('disabled', true); // disable button
                    },
                    success: function (data) {
                        if (data.type === 'success') {
                            reload_table();
                            notify_view(data.type, data.message);
                            $('#loader').hide();
                            $("#submit").prop('disabled', false); // disable button
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
                            $("#submit").prop('disabled', false); // disable button

                        }
                    }
                });
            }
            // <- end 'submitHandler' callback
        });                    // <- end '.validate()'

    });
</script>
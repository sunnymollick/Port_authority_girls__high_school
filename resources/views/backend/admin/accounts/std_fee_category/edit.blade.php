<form id='edit' action="" enctype="multipart/form-data" method="post" accept-charset="utf-8">
    <div id="status"></div>
    {{method_field('PATCH')}}
    <div class="form-group col-md-6 col-sm-12">
        <label for=""> Roles Name </label>
        <input type="text" class="form-control" id="name" name="name" value="{{ $feecategory->name }}"
               placeholder="" required>
        <span id="error_name" class="has-error"></span>
    </div>
    <div class="form-group col-md-6 col-sm-12">
        <label for=""> Class </label>
        <select name="class_id" class="form-control filter">
            @foreach($stdclass as $class)
                <option value="{{$class->id}}"
                    {{ ( $class->id == $feecategory->class_id) ? 'selected' : '' }} >{{$class->name}}</option>
            @endforeach
        </select>
        <span id="error_class_id" class="has-error"></span>
    </div>
    <div class="clearfix"></div>
    <div class="form-group col-md-12">
        <label for=""> Status </label><br/>
        <input type="radio" name="status" class="flat-green"
               value="1" {{ ( $feecategory->status == 1 ) ? 'checked' : '' }} /> Active
        <input type="radio" name="status" class="flat-green"
               value="0" {{ ( $feecategory->status == 0 ) ? 'checked' : '' }}/> In Active
    </div>
    <div class="clearfix"></div>
    <section id="option_section">
        <div class="col-md-12 form-group">
            <label for=""> Fee Items </label>
            <table id="form_table" class="table">
                <tbody>
                @if($feecategory->fee_items->count()>0)
                    @foreach($feecategory->fee_items as $items)
                        <tr class='case'>
                            <td><input type="text" class="form-control" type='text' id="item_name" name='item_name[]'
                                       value="{{ $items->item_name }}" placeholder="Fee name"/></td>
                            <td><input type="text" class="form-control each_amount" type='text' name='item_amount[]'
                                       value="{{ $items->amount }}" type="number" min="0" placeholder="Item amount" required/></td>
                            <td style='text-align:center;'><a class='btn btn-danger'><i class='fa fa-times'></i></a>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr class='case'>
                        <td><input type="text" class="form-control" type='text' id="item_name" name='item_name[]'
                                   value="" placeholder="Fee name" required/></td>
                        <td><input type="text" class="form-control each_amount" type='text' name='item_amount[]'
                                   type="number" min="0" placeholder="Item amount" required/></td>
                    </tr>
                @endif

                </tbody>
            </table>
            <button type="button" href="javascript:;" class='btn btn-success addmore pull-right'> + add more</button>
            <br>
        </div>
    </section>
    <div class="clearfix"></div>
    <div class="form-group col-md-6 col-sm-12">
        <label for=""> Total Amount </label>
        <input type="text" class="form-control" id="amount" name="amount" value="{{$feecategory->amount}}"
               placeholder="" required readonly>
        <span id="error_amount" class="has-error"></span>
    </div>
    <div class="form-group col-md-6 col-sm-12">
        <label for=""> Month </label>
        <select name="month" id="month" class="form-control" required>
            <option value="{{$feecategory->month}}"
                    selected>{!! $monthName = date("F", mktime(0, 0, 0, $feecategory->month, 10)) !!}</option>
            <option value="1">January</option>
            <option value="2">February</option>
            <option value="3">March</option>
            <option value="4">April</option>
            <option value="5">May</option>
            <option value="6">June</option>
            <option value="7">July</option>
            <option value="8">August</option>
            <option value="9">September</option>
            <option value="10">October</option>
            <option value="11">November</option>
            <option value="12">December</option>
        </select>
        <span id="error_month" class="has-error"></span>
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

        $(".addmore").on('click', function () {
            var data = "<tr class='case'>";
            data += "<td><input type='text' class='form-control' name='item_name[]' placeholder='Fee Name'/></td>"
                + "<td><input type='text' type='number' min='0' class='form-control each_amount' name='item_amount[]' placeholder='Item Amount'/></td>"
                + "<td style='text-align:center;'><a class='btn btn-danger'><i class='fa fa-times'></i></a></td></tr>";
            $('#form_table').append(data);
        });

        $('#form_table').on('click', 'tr a', function (e) {
            e.preventDefault();
            $(this).parents('tr').remove();
            totalAmount ();
        });


        $(document).on('keyup', '#form_table .each_amount', function () {

            totalAmount ();

        });

        function totalAmount () {
            var cnt = 0;
            $("input[name*='item_amount']").each(function () {
                if (isNaN(this.value)) {
                    $("#amount").val(0);
                    return 0;
                } else {
                    cnt += Number(this.value);
                }

            });

            $("#amount").val(cnt);
        }


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
                    url: 'feecategory/' + '{{ $feecategory->id }}',
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
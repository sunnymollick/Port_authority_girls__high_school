@extends('backend.layouts.master')
@section('title', 'Expense Statement')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <p class="panel-title"> Expense Statement </p>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div id="income_category">
                            <div class="form-group col-md-6 col-sm-12">
                                <label for=""> Expense Heads </label>
                                <select name="income_cat_id" id="income_cat_id" class="form-control filter" required>
                                    <option value="" selected disabled>Select Expense Heads</option>
                                    <option value="all">All</option>
                                    @foreach($category_heads as $heads)
                                        <option value="{{$heads->id}}">{{$heads->category_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-md-3 col-sm-12">
                            <label for=""> From Date </label>
                            <input type="text" class="form-control" id="from_date" name="from_date" value=""
                                   placeholder="" required>
                        </div>
                        <div class="form-group col-md-3 col-sm-12">
                            <label for=""> To Date </label>
                            <input type="text" class="form-control" id="to_date" name="to_date" value=""
                                   placeholder="" required>
                        </div>
                        <div class="clearfix"></div>
                        <div class="form-group col-md-4 col-sm-12" id="report_format_type">
                            <label for=""> Report Format </label>
                            <select name="report_format" id="report_format" class="form-control" required>
                                <option value="Summary" selected>Summary</option>
                                <option value="Details">Details</option>
                            </select>
                        </div>
                        <div class="clearfix"></div>
                        <div class="form-group  col-xl-2 col-lg-2 col-md-2 col-sm-12 mb-3 mb-lg-0">
                            <button type="button" class="btn  btn-success form-control"
                                    onclick="getExpenseStatement()"> Search
                            </button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div id="reports_content"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>

    </style>

    <script type="text/javascript">
        document.body.classList.add("sidebar-collapse");

        $(document).ready(function () {
            $('#loader').hide();
            $('.filter').select2();
            $('#from_date').datepicker({format: "yyyy-mm-dd"}).on('changeDate', function (e) {
                $(this).datepicker('hide');
            });
            $('#to_date').datepicker({format: "yyyy-mm-dd"}).on('changeDate', function (e) {
                $(this).datepicker('hide');
            });
        });


        function getExpenseStatement() {
            var income_cat_id = $("#income_cat_id").val();
            var report_format = $("#report_format").val();
            var from_date = $("#from_date").val();
            var to_date = $("#to_date").val();

            if (income_cat_id != null && from_date != '' && to_date != '') {
                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

                $.ajax({
                    type: 'GET',
                    url: 'accountsCategoryStatementReports',
                    data: {
                        "_token": CSRF_TOKEN,
                        "category_type": 'Expenses',
                        "from_date": from_date,
                        "to_date": to_date,
                        "income_cat_id": income_cat_id,
                        "report_format": report_format
                    },
                    beforeSend: function () {
                        $('body').plainOverlay('show');
                    },
                    success: function (data) {
                        $('body').plainOverlay('hide');
                        $("#reports_content").html(data.html);
                    },
                    error: function (result) {
                        $("#reports_content").html("Sorry Cannot Load Data");
                    }
                });

            } else {
                swal("Warning!!", "Please select all field", "warning");
            }
        }
    </script>

@stop
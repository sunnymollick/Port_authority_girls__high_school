@extends('backend.layouts.master')
@section('title', 'Income Statement')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <p class="panel-title"> Income Statement </p>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="form-group col-md-4 col-sm-12">
                            <label for=""> Select Income Type </label>
                            <select name="income_type" id="income_type" class="form-control" required>
                                <option value="" selected disabled="">Select Income Type</option>
                                <option value="student_fee">Student Fee</option>
                                <option value="income_category">Income Heads</option>
                                <option value="all">All</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4 col-sm-12">
                            <label for=""> From Date </label>
                            <input type="text" class="form-control" id="from_date" name="from_date" value=""
                                   placeholder="" required>
                        </div>
                        <div class="form-group col-md-4 col-sm-12">
                            <label for=""> To Date </label>
                            <input type="text" class="form-control" id="to_date" name="to_date" value=""
                                   placeholder="" required>
                        </div>
                        <div class="clearfix"></div>
                        <div id="student_section">
                            <div class="form-group col-md-6 col-sm-12">
                                <label for=""> Select Class </label>
                                <select name="class_id" id="class_id" class="form-control filter"
                                        onchange="get_sections(this.value)" required>
                                    <option value="" selected disabled>Select a class</option>
                                    <option value="all">All</option>
                                    @foreach($stdclass as $class)
                                        <option value="{{$class->id}}">{{$class->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6 col-sm-12">
                                <label for=""> Select Section </label>
                                <select class="form-control" name="section_id" id="section_id" required>
                                    <option value="all">All</option>
                                    <option value="">Select a Section</option>
                                </select>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div id="income_category">
                            <div class="form-group col-md-6 col-sm-12">
                                <label for=""> Income Heads </label>
                                <select name="income_cat_id" id="income_cat_id" class="form-control filter" required>
                                    <option value="" selected disabled>Select Income Heads</option>
                                    <option value="all">All</option>
                                    @foreach($category_heads as $incomes)
                                        <option value="{{$incomes->id}}">{{$incomes->category_name}}</option>
                                    @endforeach
                                </select>
                            </div>
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
                                    onclick="getIncomeStatement()"> Search
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

        $("#student_section").hide();
        $("#income_category").hide();

        $('#income_type').change(function () {
            var income_type = $('#income_type').val();

            if (income_type == "student_fee") {
                $("#income_category").hide();
                $("#student_section").show();
                $("#report_format_type").show();
            } else if (income_type == "income_category") {
                $("#student_section").hide();
                $("#income_category").show();
                $("#report_format_type").show();
            } else {
                $("#student_section").hide();
                $("#income_category").hide();
                $("#report_format_type").hide();
            }
        });


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

        function get_sections(val) {
            if (val != 'all') {
                $("#section_id").empty();
                $.ajax({
                    type: 'GET',
                    url: 'getAllSection/' + val,
                    success: function (data) {
                        $("#section_id").html(data);
                    },
                    error: function (result) {
                        $("#modal_data").html("Sorry Cannot Load Data");
                    }
                });
            }
        }


        function getIncomeStatement() {

            var income_type = $("#income_type").val();


            if (income_type === 'student_fee') {
                studentFeeIncomeStateMent();
            }

            if (income_type === 'income_category') {
                accountsCategroyStateMent();
            }

            if (income_type === 'all') {
                allIncomeStateMent();
            }

        }

        function studentFeeIncomeStateMent() {
            var income_type = $("#income_type").val();

            var from_date = $("#from_date").val();
            var to_date = $("#to_date").val();

            var class_id = $("#class_id").val();
            var section_id = $("#section_id").val();
            var report_format = $("#report_format").val();


            if (income_type != null && from_date != '' && to_date != '' && class_id != null && section_id != null) {

                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

                $.ajax({
                    type: 'GET',
                    url: 'studentFeeIncomeStatementReports',
                    data: {
                        "_token": CSRF_TOKEN,
                        "income_type": income_type,
                        "from_date": from_date,
                        "to_date": to_date,
                        "class_id": class_id,
                        "section_id": section_id,
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

        function accountsCategroyStateMent() {

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
                        "category_type": 'Incomes',
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


        function allIncomeStateMent() {
            var income_type = $("#income_type").val();
            var report_format = $("#report_format").val();
            var from_date = $("#from_date").val();
            var to_date = $("#to_date").val();

            if (income_type != null && from_date != '' && to_date != '') {
                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

                $.ajax({
                    type: 'GET',
                    url: 'allIncomeStatementReports',
                    data: {
                        "_token": CSRF_TOKEN,
                        "category_type": 'Incomes',
                        "income_type": income_type,
                        "from_date": from_date,
                        "to_date": to_date,
                        "report_format": 'Summary'
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
@php
    if (!$data) {
       echo "No data found"; exit();
    }
    $no = 1;
    $running_year = config('running_session');
    $year = explode('-', $running_year);
    $days = cal_days_in_month(CAL_GREGORIAN, $month, $year[0]);
    $monthName = date("F", mktime(0, 0, 0, $month, 10));
@endphp
<div class="row">
    @php
        $header_data =  "<h3> $app_settings->name</h3>" .
          "<h4>Teacher Monthly Attendance Report</h4>" .
          "<h4>Month :". $monthName . ', ' . $year[0] ." </h4>";

    @endphp
    <div class="col-md-6 col-md-offset-3 card" style="text-align: center; background: #fbfdfa">
        {!!  $header_data !!}
    </div>
</div>
<div class="col-md-12">
    <div class="table-responsive">
        <table id="manage_all" class="table table-bordered table-hover">
            <thead>
            <tr>
                <th>#</th>
                <th> Id</th>
                <th> Name</th>
                <th> Department</th>
                <th> Designation</th>
                @for ($i = 1; $i <= 31; $i++)
                    <th class="day">{{ $i }}</th>
                @endfor
                <th> TP</th>
                <th> TL</th>
                <th> TLv</th>
                <th> TA</th>
            </tr>
            </thead>
            <tbody>

            @foreach ($data as  $key => $value)
                <tr>
                    <td> {{ $no++   }} </td>
                    <td> {{ $value->teacher_id   }} </td>
                    <td> {{ $value->teacher_name   }} </td>
                    <td> {{ $value->department   }} </td>
                    <td> {{ $value->designation   }} </td>
                    <td> {{ $value->one   }} </td>
                    <td> {{ $value->two   }} </td>
                    <td> {{ $value->three  }} </td>
                    <td> {{ $value->four   }} </td>
                    <td> {{ $value->five  }} </td>
                    <td> {{ $value->six  }} </td>
                    <td> {{ $value->seven  }} </td>
                    <td> {{ $value->eight  }} </td>
                    <td> {{ $value->nine  }} </td>
                    <td> {{ $value->ten  }} </td>
                    <td> {{ $value->eleven  }} </td>
                    <td> {{ $value->twelve  }} </td>
                    <td> {{ $value->thirteen  }} </td>
                    <td> {{ $value->fourteen  }} </td>
                    <td> {{ $value->fifteen  }} </td>
                    <td> {{ $value->sixteen  }} </td>
                    <td> {{ $value->seventeen  }} </td>
                    <td> {{ $value->eightteen  }} </td>
                    <td> {{ $value->nineteen  }} </td>
                    <td> {{ $value->twenty  }} </td>
                    <td> {{ $value->twentyone  }} </td>
                    <td> {{ $value->twentytwo  }} </td>
                    <td> {{ $value->twentythree  }} </td>
                    <td> {{ $value->twentyfour  }} </td>
                    <td> {{ $value->twentyfive  }} </td>
                    <td> {{ $value->twentysix  }} </td>
                    <td> {{ $value->twentyseven  }} </td>
                    <td> {{ $value->twentyeight  }} </td>
                    <td> {{ $value->twentynine  }} </td>
                    <td> {{ $value->thirty  }} </td>
                    <td> {{ $value->thirtyone  }} </td>
                    <td> {{ $value->total_present  }} </td>
                    <td> {{ $value->total_late  }} </td>
                    <td> {{ $value->total_leave  }} </td>
                    <td> {{ $value->total_absent  }} </td>
                </tr>

            @endforeach
            </tbody>
        </table>
    </div>
</div>

<style>
    #summery td {
        font-size: 14px;
        font-weight: bold;
    }

    th, td {
        font-size: 12px;
    }

</style>

<script>
    $(document).ready(function () {

        table = $('#manage_all').DataTable({
            dom: "<'row'<'col-sm-4'><'col-sm-8'f>>" +
            "<'row'<'col-sm-12'>B>" + //
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-4'><'col-sm-8'>>",

            "lengthMenu": [[-1], ["All"]],

            "autoWidth": false,
            "scrollX": true,

            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '<i class="fa fa-table"> EXCEL </i>',
                    titleAttr: 'Excel',
                    exportOptions: {
                        columns: ':visible:not(.not-exported)'
                    }
                },
                {
                    extend: 'pdfHtml5',
                    title: " {{$app_settings->name}} \n Teacher Monthly Attendance Report \n" +
                    "Month : {{$monthName . ', ' . $year[0]}}",
                    text: '<i class="fa fa-file-pdf-o"> PDF</i>',
                    titleAttr: 'PDF',
                    orientation: 'landscape',
                    filename: 'Teacher_attendance_{{$monthName . ',' . $year[0]}}',
                    exportOptions: {
                        columns: ':visible'
                    },
                    customize: function (doc) {
                        doc.content[1].table.headerRows = 0
                        doc.pageMargins = [20, 10, 10, 10];
                        doc.defaultStyle.fontSize = 7;
                        doc.styles.tableHeader.fontSize = 7;
                        doc.styles.title.fontSize = 10;
                        // Remove spaces around page title
                        doc.content[0].text = doc.content[0].text.trim();
                        doc['footer'] = (function (page, pages) {
                            return {
                                columns: [
                                    ' {{$app_settings->name}}',
                                    {
                                        // This is the right column
                                        alignment: 'right',
                                        text: ['page ', {text: page.toString()}, ' of ', {text: pages.toString()}]
                                    }
                                ],
                                margin: [10, 0]
                            }
                        });
                    }
                },
                {
                    extend: 'print',
                    orientation: 'landscape',
                    title: "<div class='text-center'>{!! $header_data !!}</div>",
                    text: '<i class="fa fa-print"> PRINT </i>',
                    titleAttr: 'Print',
                    exportOptions: {
                        columns: ':visible'
                    }

                }

            ],

            "oSelectorOpts": {filter: 'applied', order: "current"},
            language: {
                buttons: {},

                "emptyTable": "<strong style='color:#ff0000'> Sorry!!! No Records have found </strong>",
                "search": "",
                "paginate": {
                    "next": "Next",
                    "previous": "Previous"
                },

                "zeroRecords": ""
            }
        });


        $('.dataTables_filter input[type="search"]').attr('placeholder', 'Type here to search...').css({'width': '220px'});

        $('[data-toggle="tooltip"]').tooltip();

    });
</script>

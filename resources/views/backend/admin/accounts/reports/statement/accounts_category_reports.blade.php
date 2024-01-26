@php
    if (!$data) {
       return "No data found";
    }
    $no = 1;
@endphp
<div class="row">
    @php
        $header_data =  "<h3>Khagrachari Police Lines High School</h3>" .
          "<h4>$report_format $category_type Report</h4>" .
          "<h4>From : $from_date To : $to_date</h4>";

    @endphp
    <div class="col-md-6 col-md-offset-3 card" style="text-align: center; background: #fbfdfa">
        {!!  $header_data !!}
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table id="manage_all" class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Accounts Category</th>
                    @if($report_format==='Details')
                        <th>Category Items</th>
                    @endif
                    <th class="text-right"> Total Amount</th>
                </tr>
                </thead>
                <tbody>

                @foreach ($data as  $key => $value)
                    <tr>
                        <td> {{ $no++   }} </td>
                        <td> {{ $value->category_name   }} </td>
                        @if($report_format==='Details')
                            <td>{{ $value->category_item_name   }}</td>
                        @endif
                        <td class="text-right"> {{ number_format($value->total_amount,2)   }} </td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr>
                    <th></th>
                    @if($report_format==='Details')
                        <th></th>
                    @endif
                    <th class="text-right">Grand Total</th>
                    <th class="text-right"></th>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<style>
    #summery td {
        font-size: 16px;
        font-weight: bold;
    }

    #manage_all th, td {
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

            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '<i class="fa fa-table"> EXCEL </i>',
                    titleAttr: 'Excel',
                    footer: true,
                    exportOptions: {
                        columns: ':visible:not(.not-exported)'
                    }
                },
                {
                    extend: 'pdfHtml5',
                    title: "Khagrachari Police Lines High School \n Statement Report \n" +
                    "Date : From : {{$from_date}} To : {{$to_date}}",
                    text: '<i class="fa fa-file-pdf-o"> PDF</i>',
                    titleAttr: 'PDF',
                    footer: true,
                    filename: 'Student_fee_incomes_{{$from_date}}',
                    exportOptions: {
                        columns: ':visible'
                    },
                    customize: function (doc) {
                        doc.content[1].table.headerRows = 0
                        doc.pageMargins = [150, 10, 80, 10];
                        doc.defaultStyle.fontSize = 9;
                        doc.styles.tableHeader.fontSize = 9;
                        doc.styles.tableFooter.fontSize = 9;
                        doc.styles.title.fontSize = 9;
                        // Remove spaces around page title
                        doc.content[0].text = doc.content[0].text.trim();
                        doc['footer'] = (function (page, pages) {
                            return {
                                columns: [
                                    'Khagrachari Police Lines High School',
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
                    title: "<div class='text-center'>{!! $header_data !!}</div>",
                    text: '<i class="fa fa-print"> PRINT </i>',
                    titleAttr: 'Print',
                    footer: true,
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
            },
            "footerCallback": function (row, data, start, end, display) {
                var api = this.api(), data;


                // Remove the formatting to get integer data for summation
                var intVal = function (i) {
                    return typeof i === 'string' ?
                        i.replace(/[\$,]/g, '') * 1 :
                        typeof i === 'number' ?
                            i : 0;
                };


                // Total Returned Items
                var column_no = " {{$report_format==='Details'? 3 : 2}} ";
                total = api
                    .column(column_no)
                    .data()
                    .reduce(function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);
                // Update footer
                $(api.column(column_no).footer()).html(
                    total.toFixed(2)
                );


            }
        });


        $('.dataTables_filter input[type="search"]').attr('placeholder', 'Type here to search...').css({'width': '220px'});

        $('[data-toggle="tooltip"]').tooltip();

    });
</script>

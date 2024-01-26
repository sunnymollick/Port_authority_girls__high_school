@php
    if (!$data) {
       echo "No data found"; exit();
    }
    $no = 1;
    $total_student = 0;
    $total_present = 0;
    $total_absent = 0;
    $total_leave = 0;
    foreach ($data as $key => $value) {
       if ($value->attn_status === 'P' || $value->attn_status === 'L') {
          $total_present = $total_present + 1;
       }
       if ($value->attn_status === 'A') {
          $total_absent = $total_absent + 1;
       }
       if ($value->attn_status === 'Lv') {
          $total_leave = $total_leave + 1;
       }
    }
@endphp
<div class="row">
    <hr/>
    @php
        $header_data =  "<h3>$app_settings->name</h3>" .
          "<h4>Student Daily Attendance Report</h4>" .
          "<h4>Class : $class_name </h4>" .
          "<h4>Section : $section_name</h4>" .
          "<h4>Date : $atten_date </h4>";

    @endphp
    <div class="col-md-6 col-md-offset-3 card" style="text-align: center; background: #fbfdfa">
        {!!  $header_data !!}
    </div>
</div>
<div class="row">
    <hr/>
    <div class="col-md-12">
        <table id="summery" class="table table-bordered table-hover">
            <tbody>
            <tr>
                <td>Total Student : {{ count($data) }} </td>
                <td>Present Student : {{ $total_present }}</td>
                <td>Student in Leave: {{ $total_leave }}</td>
                <td>Absent : {{ $total_absent }}</td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table id="manage_all" class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th>#</th>
                    <th> Student Id</th>
                    <th> Student Name</th>
                    <th> Class</th>
                    <th> Section</th>
                    <th> In Time</th>
                    <th> Out Time</th>
                    <th> Late Minutes</th>
                    <th> Status</th>
                    <th> Remarks</th>
                </tr>
                </thead>
                <tbody>

                @foreach ($data as  $key => $value)
                    <tr>
                        <td> {{ $no++   }} </td>
                        <td> {{ $value->std_code   }} </td>
                        <td> {{ $value->std_name   }} </td>
                        <td> {{ $class_name   }} </td>
                        <td> {{ $section_name   }} </td>
                        <td> {{ $value->in_time   }} </td>
                        <td> {{ $value->out_time   }} </td>
                        <td> {{ $value->late  }} </td>
                        <td> {{ $value->attn_status   }} </td>
                        <td> {{ $value->remarks  }} </td>
                    </tr>

                @endforeach
                </tbody>
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
                    exportOptions: {
                        columns: ':visible:not(.not-exported)'
                    }
                },
                {
                    extend: 'pdfHtml5',
                    title: " {{$app_settings->name}} \n Teacher Daily Attendance Report \n" +
                    "Class : {{ $class_name   }} \n" +
                    "Section : {{ $section_name   }} \n" +
                    "Date : {{$atten_date}}",
                    text: '<i class="fa fa-file-pdf-o"> PDF</i>',
                    titleAttr: 'PDF',
                    filename: 'Student_attendance_{{$class_name}}_{{$section_name}}_{{$atten_date}}',
                    exportOptions: {
                        columns: ':visible'
                    },
                    customize: function (doc) {
                        doc.content[1].table.headerRows = 0
                        doc.pageMargins = [80, 10, 80, 10];
                        doc.defaultStyle.fontSize = 8;
                        doc.styles.tableHeader.fontSize = 7;
                        doc.styles.title.fontSize = 8;
                        // Remove spaces around page title
                        doc.content[0].text = doc.content[0].text.trim();
                        doc['footer'] = (function (page, pages) {
                            return {
                                columns: [
                                    '{{ $app_settings->name }}',
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
                    exportOptions: {
                        columns: ':visible'
                    }

                }, {
                    extend: 'colvis',
                    text: '<i class="fa fa-eye-slash"> Column Visibility </i>',
                    titleAttr: 'Visibility'
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

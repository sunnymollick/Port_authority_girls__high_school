@if(!empty($data))
    <hr/>
    <div class="row">
        <hr/>
        @php
            $total_std = 0;
            $pass = 0;
            $fail = 0;
            $cgpa = 0.00;

            $exam_name =  $data['exam_name'] ;
            $section_name =  $data['section_name'] ;
            $class_name =  $data['class_name'] ;
                $header_data =  "<h4>$app_settings->name</h4>" .
                  "<h4>$exam_name</h4>" .
                  "<h4>Class : $class_name </h4>" .
                  "<h4>Section : $section_name</h4>";

        @endphp
    </div>
    <div class="col-md-12">
        <div class="col-md-5 col-md-offset-3">
            <div class="card card_text">
                <div class="card-body text-center">
                    {!!  $header_data !!}
                </div>
            </div>
            <hr/>
        </div>
    </div>
    <div id="status"></div>
    <img id="loaderSubmit" src="{{asset('assets/images/loadingg.gif')}}" width="20px">
    <div class="col-md-12 col-sm-12 table-responsive">
        @if(!$session_year->special)
            <table id="manage_all" class="table table-collapse table-bordered table-hover">
                <thead>
                <tr>
                    <th class="serial">#</th>
                    <th class="std_id">Student's ID</th>
                    <th class="std_name">Student's Name</th>
                    <th class="text-center">Student's Roll</th>
                    <th class="text-center"> Marksheet</th>
                </tr>
                </thead>
                <tbody>
                @foreach($data['result'] as $row)
                    <tr>
                        <td>{{ $row->rownum }}</td>
                        <td>{{ $row->stdCode }}</td>
                        <td>{{ $row->stdName }}</td>
                        <td class="text-center">{{ $row->stdRoll }}</td>
                        <td class="text-center"><a data-toggle='tooltip' title='View Tabulation Sheet'
                                                class="btn btn-success view" std_roll="{{ $row->stdRoll }}"
                                                std_name="{{ $row->stdName }}"
                                                std_code="{{ $row->stdCode }}"> View </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="4"></td>
                    <td></td>
                </tr>
                </tfoot>
            </table>
        @else
            <table id="manage_all" class="table table-collapse table-bordered table-hover">
                <thead>
                <tr>
                    <th class="serial">#</th>
                    <th class="std_id">Student's ID</th>
                    <th class="std_name">Student's Name</th>
                    <th class="text-center">Student's Roll</th>
                    <th class="text-center">Grade</th>
                    <th class="text-center">GP</th>
                    <th class="text-center">Result Status</th>
                    <th class="text-center">Rank</th>
                    <th class="text-center"> Marksheet</th>
                </tr>
                </thead>
                <tbody>
                @foreach($data['result'] as $row)
                @php

                        $total_std = $total_std+1;
                        $total_subjects = $row->totalSubject;
                        $cgpaPoint = $row->mainSubPoint;

                        if($row->result == 'PASSED'){

                            $cgpa = sprintf('%0.2f', $cgpaPoint / $total_subjects);

                            if ($cgpa >= 5) {
                                $gpa = "A+";
                            } else if ($cgpa >= 4 and $cgpa <= 4.99) {
                                $gpa = "A";
                            } else if ($cgpa >= 3.50 and $cgpa <= 3.99) {
                                $gpa = "A-";
                            } else if ($cgpa >= 3 and $cgpa <= 3.49) {
                                $gpa = "B";
                            } else if ($cgpa >= 2 and $cgpa <= 2.99) {
                                $gpa = "C";
                            } else if ($cgpa >= 1 and $cgpa <= 1.99) {
                                $gpa = "D";
                            } else {
                                $gpa = "F";
                            }

                            $pass = $pass + 1;
                            $status = '<span class="label label-success">PASSED</span>';
                        }else{
                            $gpa = "F";
                            $cgpa = '0.00';
                            $fail = $fail + 1;
                            $status = '<span class="label label-danger">FAILED</span>';
                        }

                    $cgpa = $cgpa>5? '5.00':$cgpa;
                    @endphp
                    <tr>
                        <td>{{ $row->rownum }}</td>
                        <td>{{ $row->stdCode }}</td>
                        <td>{{ $row->stdName }}</td>
                        <td class="text-center">{{ $row->stdRoll }}</td>
                        <td class="text-center">{!! $gpa !!}</td>
                        <td class="text-center">{{ $cgpa }}</td>
                        <td class="text-center">  {!! $status !!}   </td>
                        @if((int)$row->rank<10)
                            <td class="text-center"><span>&nbsp;&nbsp;</span>{{$row->rank}}</td>
                        @elseif((int)$row->rank<100)
                            <td class="text-center"><span>&nbsp;</span>{{$row->rank}}</td>
                        @else
                            <td class="text-center">{{$row->rank}}</td>
                        @endif
                        <td class="text-center"><a data-toggle='tooltip' title='View Tabulation Sheet'
                                                class="btn btn-success view" std_roll="{{ $row->stdRoll }}"
                                                std_name="{{ $row->stdName }}"
                                                std_code="{{ $row->stdCode }}"
                                                rank="{{ $row->rank }}"> View </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="4"></td>
                    <td></td>
                </tr>
                </tfoot>
            </table>
        @endif
    </div>
@else
    <div class="row">
        <div class="col-md-12 text-center">
            <div id="not_found">
                <img src="{{asset('assets/images/empty_box.png')}}" width="200px">
            </div>
            <h2>No data found of this requirement</h2>
        </div>
    </div>
@endif
<style>
    .serial {
        width: 5%;
    }

    .std_id {
        width: 15%;
    }

    .std_name {
        width: 27%;
    }
</style>
<script type="text/javascript">
    $('#loaderSubmit').hide();
    $("#manage_all").on("click", ".view", function () {
        $("#modal_data").empty();
        $('.modal-title').text('View Marksheet');

        var std_code = $(this).attr('std_code');
        var std_name = $(this).attr('std_name');
        var std_roll = $(this).attr('std_roll');
        var rank = $(this).attr('rank');
        var class_id = "{{ $data['class_id'] }}";
        var section_id = "{{ $data['section_id'] }}";
        var exam_id = "{{ $data['exam_id'] }}";
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        // alert(std_code + ' / ' + class_id + ' / ' + section_id + ' / ' + exam_id);

        $.ajax({
            url: 'srMarksheet',
            type: "POST",
            data: {
                "class_id": class_id,
                "section_id": section_id,
                "exam_id": exam_id,
                "student_code": std_code,
                "student_name": std_name,
                "std_roll": std_roll,
                "rank": rank,
                "exam_name": "{{ $data['exam_name'] }}",
                "section_name": "{{ $data['section_name'] }}",
                "class_name": "{{ $data['class_name'] }}",
                "_token": CSRF_TOKEN
            },
            dataType: 'json',
            beforeSend: function () {
                $('body').plainOverlay('show');
            },
            success: function (data) {
                $('body').plainOverlay('hide');
                $("#modal_data").html(data.html);
                $('#myModal').modal('show'); // show bootstrap modal
            },
            error: function (result) {
                $("#modal_data").html("Sorry Cannot Load Data");
            }
        });
    });
</script>
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
                    title: " {{$app_settings->name}} \n {{ $exam_name   }}  \n" +
                    "Class : {{ $class_name   }} \n" +
                    "Section : {{ $section_name   }} \n",
                    text: '<i class="fa fa-file-pdf-o"> PDF</i>',
                    titleAttr: 'PDF',
                    footer: true,
                    filename: '{{ $exam_name   }}_{{$class_name}}_{{$section_name}}',
                    exportOptions: {
                        columns: ':visible'
                    },
                    customize: function (doc) {
                        doc.content[1].table.headerRows = 0
                        doc.pageMargins = [30, 10, 10, 10];
                        doc.defaultStyle.fontSize = 8;
                        doc.styles.tableHeader.fontSize = 7;
                        doc.styles.title.fontSize = 9;
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
                    footer: true,
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

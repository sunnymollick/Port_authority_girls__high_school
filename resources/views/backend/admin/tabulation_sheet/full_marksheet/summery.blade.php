@if(!empty($data))
    <hr/>
    <div class="row">
        <hr/>
        @php
            $exam_name =  $data['exam_name'] ;
            $section_name =  $data['section_name'] ;
            $class_name =  $data['class_name'] ;
                $header_data =  "<h4>$app_settings->name</h4>" .
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
        <table id="manage_all" class="table table-collapse table-bordered table-hover">
            <thead>
            <tr>
                <th class="serial">#</th>
                <th class="std_id">Student's ID</th>
                <th class="std_name">Student's Name</th>
                <th class="text-center">Class</th>
                <th class="text-center">Section</th>
                <th class="text-center">Roll</th>
                <th class="text-center"> Marksheet</th>
            </tr>
            </thead>
            <tbody>
            @foreach($data['result'] as $row)
                <tr>
                    <td>{{ $row->rownum }}</td>
                    <td>{{ $row->std_code }}</td>
                    <td>{{ $row->std_name }}</td>
                    <td>{{ $row->class_name }}</td>
                    <td>{{ $row->section_name }}</td>
                    <td class="text-center">{{ $row->roll }}</td>
                    <td class="text-center"><a data-toggle='tooltip' title='View Mark Sheet'
                                               class="btn btn-success view" std_roll="{{ $row->roll }}"
                                               std_name="{{ $row->std_name }}"
                                               std_code="{{ $row->std_code }}"> View </a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
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
        width: 30%;
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
        var class_id = "{{ $data['class_id'] }}";
        var section_id = "{{ $data['section_id'] }}";
        var first_term = "{{ $data['first_term'] }}";
        var second_term = "{{ $data['second_term'] }}";
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        // alert(std_code + ' / ' + class_id + ' / ' + section_id + ' / ' + exam_id);

        $.ajax({
            url: 'viewFullMarksheet',
            type: "POST",
            data: {
                "class_id": class_id,
                "section_id": section_id,
                "student_code": std_code,
                "student_name": std_name,
                "std_roll": std_roll,
                "first_term": first_term,
                "second_term": second_term,
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

            "lengthMenu": [[-1], ["All"]],
            "autoWidth": false,
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
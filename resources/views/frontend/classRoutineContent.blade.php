<div class="col-md-12 col-sm-12" id="class_routine">
    <div class="row" id="header_details">
        <div class="col-md-4 col-sm-12 col-md-offset-4">
            <p style="text-transform: uppercase; font-size: 14px; font-weight: bold; text-align: center">
                {{ $app_settings ? $app_settings->name : '' }} <br/>
                <strong>Class : </strong> {{ $data['class_name'] }} <br/>
                <strong>Section : </strong> {{ $data['section_name'] }} <br/>
            </p>
        </div>
    </div>
    <table class="table table-hover table-bordered table-striped">
        <tbody>
        @for($d=1;$d<=7;$d++)
            @php
                if($d==1)$day='saturday';
                else if($d==2)$day='sunday';
                else if($d==3)$day='monday';
                else if($d==4)$day='tuesday';
                else if($d==5)$day='wednesday';
                else if($d==6)$day='thursday';
                else if($d==7)$day='friday';
            @endphp
            <tr>
                <td class="day" style="vertical-align : middle;">{{$day}}</td>
                <td align="left">
                    @foreach($data['routines'] as $row)
                        @if($row->day ===$day)
                            <div class="btn-group text-left">
                                <button type="button" class="btn btn-success dropdown-toggle"data-toggle="dropdown"
                                        aria-expanded="false">
                                    <p style="margin-bottom: 0px;color:white;"><i
                                            class="fa fa-book"></i> {{ $row->subject_name }}</p>
                                    <p style="margin-bottom: 0px;color:white;"><i
                                            class="fa fa-clock-o"></i>
                                        {{ ( $row->time_start <= 12) ? $row->time_start. '.' .$row->time_start_min. ' AM' : ($row->time_start-12). '.' .$row->time_start_min. ' PM' }}
                                        -
                                        {{ ( $row->time_end <= 12) ? $row->time_end. '.' .$row->time_end_min. ' AM' : ($row->time_end-12). '.' .$row->time_end_min. ' PM' }}
                                    </p>
                                    <p style="margin-bottom: 0px;color:white;"><i
                                            class="fa fa-user"></i> {{ $row->teacher_name }}</p>
                                    <p style="margin-bottom: 0px;color:white;"><i
                                            class="fa fa-home"></i> Room - {{ $row->class_room }}</p>

                                </button>

                            </div>
                        @endif
                    @endforeach
                </td>
            </tr>
        @endfor
        </tbody>
    </table>
</div>
<hr/>
<div class="col-md-12">
    <button type='button' id='btn' class='btn btn-success pull-right' value='Print'
            onClick='printContent();'>Print Class Routine
    </button>
</div>
<style>
    .day {
        width: 100px;
        text-transform: capitalize;
        font-weight: bold;
        color: #747172;
        text-align: center;
    }
    table th, td {
        font-size: 10px;
    }
    #class_routine p{
        font-size: 10px;
    }
</style>
<script>
    function printContent() {
        $('#class_routine').printThis({
            importCSS: true,
            importStyle: true,//thrown in for extra measure
            loadCSS: "{{ asset('/assets/css/class_routine.css') }}",
            //header: '<h1> Table Report</h1>',

        });
    }
</script>

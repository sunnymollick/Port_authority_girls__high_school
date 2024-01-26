<hr/>
@php

    $no = 1;
    $running_year = config('running_session');
    $year = explode('-', $running_year);
    $monthName = date("F", mktime(0, 0, 0, $data['month'], 10));
$class_name = $data['class_name'];
$section_name = $data['section_name'];

    $total_days = 0;
    $total_present = 0;
    $total_absent = 0;
    $total_leave = 0;
    $number_words = array('', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten', 'eleven',
        'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eightteen', 'nineteen','twenty','twentyone','twentytwo',
        'twentythree','twentyfour','twentyfive','twentysix','twentyseven','twentyeight','twentynine','thirty','thirtyone'
    );

@endphp
<div class="row">
    @php
        $header_data =  "<h3>$app_settings->name</h3>" .
          "<h4>Student Monthly Attendance Report</h4>" .
          "<h4>Class : $class_name</h4>" .
          "<h4>Section : $section_name</h4>" .
          "<h4>Month : ". $monthName . ', ' . $year[0] ." </h4>";

    @endphp
    <div class="col-md-6 col-md-offset-3 card" style="text-align: center; background: #fbfdfa">
        {!!  $header_data !!}
    </div>
</div>
<div class="row">
    <div class="col-md-12"><br/>
        <hr/>
        <table id="summery" class="table table-bordered table-hover">
            <tbody>
            <tr class="text-bold">
                <td>Total Days : {{ $data['result'][0]->total_absent }} </td>
                <td>Present : {{ $data['result'][0]->total_present }}</td>
                <td>Leave: {{ $data['result'][0]->total_leave }}</td>
                <td>Absent : {{ $data['result'][0]->total_absent }}</td>
                <td>Late : {{ $data['result'][0]->total_late }}</td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
<div class="col-md-12 col-sm-12 table-responsive">
    <table id="manage_all" class="table table-collapse table-bordered table-hover">
        <thead>
        <tr>
            <th>Date</th>
            <th>Day</th>
            <th>Attendance Status</th>
        </tr>
        </thead>
        <tbody>
        @foreach($data['result'] as $key=>$row)
            @for($i=1; $i<31 ; $i++)
                @php
                    $c_date = $data['month'] . '/' . $i . '/' . $year[0];
                    $words = $number_words[$i];
                    $date = $i . '/' . $data['month'] . '/' . $year[0];
                    $nameOfDay = date('l', strtotime($c_date));

                @endphp
                <tr class="text-bold">
                    <td>{{ $date }}</td>
                    <td>{{ $nameOfDay }}</td>
                    <td>{{ $row->$words }}</td>
                </tr>
            @endfor
        @endforeach
        </tbody>
    </table>
</div>
<style>
    th, td {
        font-size: 12px;
    }

    .serial {
        width: 5%;
    }

    .std_id {
        width: 5%;
    }

    .std_name {
        width: 18%;
    }

    .green {
        color: #0aa124;
    }

    .red {
        color: #d71001;
    }

    .blue {
        color: #3c6db2;
    }
</style>



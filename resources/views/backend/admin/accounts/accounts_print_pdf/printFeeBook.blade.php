<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <style type="text/css">
        #invoice {
            width: 100%;
            display: block;
        }

        #col_1, #col_2, #col_3 {
            width: 33.3%;
            float: left;
            position: relative;
        }

        .heading p {
            text-align: center;
            font-size: 11px;
            margin-left: -80px;
        }

        .footer p {
            text-align: center;
            font-size: 11px;
        }

        .h_month {
            font-weight: bold;
            font-size: 16px;
        }

        .h_name {
            font-weight: bold;
        }

        table {
            width: 80%;
        }

        table th, td {
            text-align: left;
            border: 1px solid #727b83;
            font-size: 11px;
            padding: 5px;
        }

        #table_1 .amount {
            text-align: right;
        }

        #table_2 .amount {
            text-align: right;
        }

        #table_3 .amount {
            text-align: right;
        }

        #footer {
            display: block;
            width: 75%;
        }

        .std_prnt {
            width: 20%;
            float: left;
        }

        .std_prnt p {
            text-align: left;
            font-size: 11px;
        }

        .bank_counter {
            position: relative;
            width: 55%;
            float: right;
        }

        .bank_counter p {
            text-align: right;
            font-size: 11px;
        }

        #barcode h4 {
            text-align: center;
            margin-left: -80px;
        }

        .barcode-item {
            margin-left: 1px;
        }

        .barcode-code {
            margin-left: 30%;
            margin-top: -50px;
        }
    </style>
</head>
<body style="page-break-after: auto">
@php
    $month = sprintf("%02d", $monthly[0]->month);
   // $barcode = $monthly[0]->std_code. $month.date('y');
@endphp
<div id="invoice">
    <div id="col_1">
        <div class="heading">
            <p class="h_month">[{!! $monthName = date("F", mktime(0, 0, 0, $month , 10)) !!}]
                [{{ config('running_session') }}]</p>
            <p class="h_name">Khagrachari Police Lines High School</p>
            <p>Student Copy</p>
            <p class="h_name">Student Name : {{$monthly[0]->name}} [{{ $monthly[0]->std_code }}]</p>
            <p class="h_name">Class: {{ $monthly[0]->class_name }}, Section : {{ $monthly[0]->section_name }}, Roll
                : {{ $monthly[0]->roll }}</p>
        </div>
        @php $i = 1 @endphp
        <table id="table_1">
            <thead>
            <tr>
                <th>SL</th>
                <th>Head of the Fund</th>
                <th class="amount">Amount (Tk.)</th>
            </tr>
            </thead>
            @php $sum = 0; @endphp
            <tbody>
            @foreach($monthly as $items)
                @php $sum += $items->amount;  @endphp
                <tr>
                    <td>{{ $i++ }}</td>
                    <td>{{ $items->acccounts_head }}</td>
                    <td class="amount">{{ number_format($items->amount,2) }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="2">{{ "Total Amount" }}</td>
                <td class="amount h_name">{{ number_format($sum,2)  }}</td>
            </tr>
            </tbody>
        </table>
        <div id="clearfix"></div>

        <br/>
        <div id="footer">
            <div class="std_prnt">
                <p> -----------------<br/>
                    Student/Parent</p>
            </div>
            <div class="bank_counter">
                <p> -----------------<br/>
                    Bank Counter's</p>
            </div>
        </div>
        <div id="clearfix"></div>
        <br/>
        <div id="barcode">
            <br/><br/>
            <h4 class="text-center">Please don't write below code</h4> <br/>
            <div class="barcode-item">
                <img src="data:image/png;base64,{{DNS1D::getBarcodePNG($monthly[0]->barcode, 'C39',1.2,65)}}"
                     alt="barcode"/>
                <span class="barcode-code">{{$monthly[0]->barcode}}</span>
            </div>
        </div>
    </div>
    <div id="col_2">
        <div class="heading">
            <p class="h_month">[{!! $monthName = date("F", mktime(0, 0, 0, $month, 10)) !!}]
                [{{ config('running_session') }}]</p>
            <p class="h_name">Khagrachari Police Lines High School</p>
            <p>Bank Copy</p>
            <p class="h_name">Student Name : {{$monthly[0]->name}} [{{ $monthly[0]->std_code }}]</p>
            <p class="h_name">Class: {{ $monthly[0]->class_name }}, Section : {{ $monthly[0]->section_name }}, Roll
                : {{ $monthly[0]->roll }}</p>
        </div>
        @php $i = 1 @endphp
        <table id="table_2">
            <thead>
            <tr>
                <th>SL</th>
                <th>Head of the Fund</th>
                <th class="amount">Amount (Tk.)</th>
            </tr>
            </thead>
            @php $sum = 0; @endphp
            <tbody>
            @foreach($monthly as $items)
                @php $sum += $items->amount;  @endphp
                <tr>
                    <td>{{ $i++ }}</td>
                    <td>{{ $items->acccounts_head }}</td>
                    <td class="amount">{{  number_format($items->amount,2) }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="2">{{ "Total Amount" }}</td>
                <td class="amount h_name">{{ number_format($sum,2)  }}</td>
            </tr>
            </tbody>
        </table>
        <div id="clearfix"></div>

        <br/>
        <div id="footer">
            <div class="std_prnt">
                <p> -----------------<br/>
                    Student/Parent</p>
            </div>
            <div class="bank_counter">
                <p> -----------------<br/>
                    Bank Counter's</p>
            </div>
        </div>
        <div id="clearfix"></div>
        <br/>
        <div id="barcode">
            <br/><br/>
            <h4 class="text-center">Please don't write below code</h4> <br/>
            <div class="barcode-item">
                <img src="data:image/png;base64,{{DNS1D::getBarcodePNG($monthly[0]->barcode, 'C39',1.2,65)}}"
                     alt="barcode"/>
                <span class="barcode-code">{{$monthly[0]->barcode}}</span>
            </div>
        </div>
    </div>
    <div id="col_3">
        <div class="heading">
            <p class="h_month">[{!! $monthName = date("F", mktime(0, 0, 0, $month, 10)) !!}]
                [{{ config('running_session') }}]</p>
            <p class="h_name">Khagrachari Police Lines High School</p>
            <p>School Copy</p>
            <p class="h_name">Student Name : {{$monthly[0]->name}} [{{ $monthly[0]->std_code }}]</p>
            <p class="h_name">Class: {{ $monthly[0]->class_name }}, Section : {{ $monthly[0]->section_name }}, Roll
                : {{ $monthly[0]->roll }}</p>
        </div>
        @php $i = 1 @endphp
        <table id="table_3">
            <thead>
            <tr>
                <th>SL</th>
                <th>Head of the Fund</th>
                <th class="amount">Amount (Tk.)</th>
            </tr>
            </thead>
            @php $sum = 0; @endphp
            <tbody>
            @foreach($monthly as $items)
                @php $sum += $items->amount;  @endphp
                <tr>
                    <td>{{ $i++ }}</td>
                    <td>{{ $items->acccounts_head }}</td>
                    <td class="amount">{{ number_format($items->amount,2) }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="2">{{ "Total Amount" }}</td>
                <td class="amount h_name">{{ number_format($sum,2) }}</td>
            </tr>
            </tbody>
        </table>
        <div id="clearfix"></div>

        <br/>
        <div id="footer">
            <div class="std_prnt">
                <p> -----------------<br/>
                    Student/Parent</p>
            </div>
            <div class="bank_counter">
                <p> -----------------<br/>
                    Bank Counter's</p>
            </div>
        </div>
        <div id="clearfix"></div>
        <br/>
        <div id="barcode">
            <br/><br/>
            <h4 class="text-center">Please don't write below code</h4> <br/>
            <div class="barcode-item">
                <img src="data:image/png;base64,{{DNS1D::getBarcodePNG($monthly[0]->barcode, 'C39',1.2,65)}}"
                     alt="barcode"/>
                <span class="barcode-code">{{$monthly[0]->barcode}}</span>
            </div>
        </div>
    </div>
</div>
</body>




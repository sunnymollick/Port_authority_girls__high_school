<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>
<body style="page-break-after: auto">
@if(!empty($data))
    <div class="" id="marksheet">
        <div id="header_details">
            <div class="header_image_title">


                <div style="margin-left: 80px;">
                    <h1 style="font-size:34px; color: #1f2698; font-weight: bolder; padding:0; margin:0; text-align: center;">&nbsp;<br>&nbsp;{{ $app_settings ? $app_settings->name : '' }}</h1>
                    <p style="text-align: center; padding:0; margin:0; ">
                        <strong style="font-size:30px; color: green;">{{ $data['exam_name'] }} </strong>
                    </p>
                    <p style="text-align: center; padding:0; margin:0; margin-top: 10px; font-size: 26px;">
                        <strong><span style="color: #923495; ">Class :</span> </strong> {{ $data['class_name'] }}
                    </p>
                    <p style="text-align: center; padding:0; margin:0; margin-top: 10px; font-size: 26px;">
                        <strong><span style="color: #923495; ">Summary</span> </strong>
                    </p>

                </div>
                <div style="position: absolute; top:15px; left: 230px; ">
                        <img style="border: none;" src="{{ asset('assets/images/port_school_logo.png') }}"
                    class="img-responsive img-thumbnail"
                    width="80px"/>
                </div>
                {{-- <strong style="padding-left:115px; padding-top:100px; font-size:16px;"> Exam : {{ $data['exam_name'] }} </strong> --}}
            </div>


        </div>
        &nbsp;

{{-- @php
    dd("test");
@endphp --}}
            <div id="marks_table">
                <table id="table_1">
                    <thead class="divTableHeading">
                    <tr>
                        <th > Section</th>
                        <th> A+</th>
                        <th> A</th>
                        <th> A-</th>
                        <th> B</th>
                        <th> C</th>
                        <th> D</th>
                        <th> F</th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Golap</td>
                            <td>{{ $grade_summary["Golap"]["A+"]}}</td>
                            <td>{{ $grade_summary["Golap"]["A"]}}</td>
                            <td>{{ $grade_summary["Golap"]["A-"]}}</td>
                            <td>{{ $grade_summary["Golap"]["B"]}}</td>
                            <td>{{ $grade_summary["Golap"]["C"]}}</td>
                            <td>{{ $grade_summary["Golap"]["D"]}}</td>
                            <td>{{ $grade_summary["Golap"]["F"]}}</td>
                        </tr>
                        <tr>
                            <td>Shapla</td>
                            <td>{{ $grade_summary["Shapla"]["A+"]}}</td>
                            <td>{{ $grade_summary["Shapla"]["A"]}}</td>
                            <td>{{ $grade_summary["Shapla"]["A-"]}}</td>
                            <td>{{ $grade_summary["Shapla"]["B"]}}</td>
                            <td>{{ $grade_summary["Shapla"]["C"]}}</td>
                            <td>{{ $grade_summary["Shapla"]["D"]}}</td>
                            <td>{{ $grade_summary["Shapla"]["F"]}}</td>
                        </tr>

                    </tbody>
                    <tfoot >
                    <tr>
                        <td>All</td>
                        <td>{{ $grade_summary["Golap"]["A+"] + $grade_summary["Shapla"]["A+"]}}</td>
                        <td>{{ $grade_summary["Golap"]["A"] + $grade_summary["Shapla"]["A"]}}</td>
                        <td>{{ $grade_summary["Golap"]["A-"] + $grade_summary["Shapla"]["A-"]}}</td>
                        <td>{{ $grade_summary["Golap"]["B"] + $grade_summary["Shapla"]["B"]}}</td>
                        <td>{{ $grade_summary["Golap"]["C"] + $grade_summary["Shapla"]["C"]}}</td>
                        <td>{{ $grade_summary["Golap"]["D"] + $grade_summary["Shapla"]["D"]}}</td>
                        <td>{{ $grade_summary["Golap"]["F"] + $grade_summary["Shapla"]["F"]}}</td>

                    </tr>
                    </tfoot>
                </table>
            </div>

        <br/>

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
{{-- <style>

    .points {
        color: #0b2e13;
    }

    #header_details {
        width: 100%;
        display: block;
    }

    #header_details p {
        font-size: 15px;
    }

    #marks_table {
        position: relative;
        width: 100%;
        display: block;
        margin-top: 60px;
    }

    .divTableHeading {
        background-color: #eee;
        display: table-header-group;
        font-weight: bold;
    }

    #col_1, #col_2, #col_3 {
        width: 33.3%;
        float: left;
        position: relative;
    }

    #manage_all_result td, th {
        text-align: center;
    }

    input {
        border: 1px solid #f6f6f6;
    }

    .heading p {
        text-align: center;
        font-size: 13px;
        margin-left: -80px;
    }

    .footer p {
        text-align: center;
        font-size: 13px;
    }

    table th, td {
        /* text-align: left; */
        border: 1px solid #727b83;
        font-size: 13px;
        padding: 1px;
        overflow: hidden;
        line-height: 18px;
    }

    #table_1 {
        text-align: center;
        border-collapse: collapse;
        width: 100%;
        line-height: 18px;
    }

    #table_2 {
        border-collapse: collapse;
        width: 60%;
        float: left;
        text-align: left;
        line-height: 18px;
    }

    #table_3 {
        width: 38%;
        float: right;
        line-height: 18px;
        text-align: center;
    }

    #table_2 td:nth-child(1) {
        text-align: right;
    }

    #table_1 td:nth-child(1) {
        font-weight: bold;
    }
    .header_image img {
        float: left;
        margin-left: 280px;

    }

    .header_image h1 {
        position: relative;
        top: 18px;
        left: 10px;
    }


</style> --}}
<style>

    .points {
        color: #0b2e13;
    }

    #header_details {
        width: 100%;
        display: block;
    }

    #header_details p {
        font-size: 15px;
    }

    #marks_table {
        position: relative;
        width: 100%;
        display: block;
        /* margin-top: 60px; */
    }

    .divTableHeading {
        background-color: #eee;
        display: table-header-group;
        font-weight: bold;
    }

    #col_1, #col_2, #col_3 {
        width: 33.3%;
        float: left;
        position: relative;
    }

    #manage_all_result td, th {
        text-align: center;
    }

    input {
        border: 1px solid #f6f6f6;
    }

    .heading p {
        text-align: center;
        font-size: 13px;
        margin-left: -80px;
    }

    .footer p {
        text-align: center;
        font-size: 13px;
    }

    table th, td {
        /* text-align: left; */
        border: 1px solid #727b83;
        font-size: 13px;
        padding: 1px;
        overflow: hidden;
        line-height: 18px;
    }

    #table_1 {
        text-align: center;
        border-collapse: collapse;
        width: 100%;
        line-height: 18px;
    }

    #table_2 {
        border-collapse: collapse;
        width: 100%;
        /* float: left; */
        text-align: left;
        line-height: 18px;
    }

    #table_3 {
        width: 100%;
        /* float: right; */
        line-height: 18px;
        text-align: center;
    }



    #table_2 td:nth-child(1) {
        text-align: right;
    }

    #table_1 td:nth-child(1) {
        font-weight: bold;
        color: green;
    }
    #table_1 td:nth-child(2) {
        /* font-weight: bold; */
    }

    .header_image img {
        /* float: left;
        margin-left: 280px; */

    }

    .header_image h1 {
        /* position: relative;
        top: 18px;
        left: 10px; */
    }
    .header_image_title{
        position: relative;
    }

    #sum_1{
        float: left;
        width: 60%;
    }

    #sum_2{
        margin-left: 65%;
        width: 35%;
    }
    body {
        border: 7px solid #ab8731;
        padding: 0 15px;
        box-sizing: border-box;
        border-radius: 5px;
    }
    #table_1 th{
        background: #a0ddfc;
        color: blue;
        font-size: 24px;
        padding: 10px 0;
    }
    #table_1 td{
        font-size: 20px;
        padding: 10px 0;
    }

    #table_3 th{
        background: #d3e6af;
        color: green;
    }
    #table_3 td{
        color: green;
    }
    #table_2 td:nth-child(1) {
        text-align: right;
        color: maroon;
    }





</style>
</body>

@extends('frontend.layouts.master')
@section('title', ' Management Committee')
@section('content')
    <div class="container m-top-60">
        <div class="section-title text-center">
            <h3>Our Honourable Management Committee</h3>
        </div>
        <div class="row">
            <div class="col-md-3 col-sm-12">
                <div class="member">
                    <div class="member-pic set-bg"
                         data-setbg="{{ asset('assets/images/committee/Md_Ahmar_Uzzaman.jpg') }}">
                    </div>
                    <h5>Md. Ahmar Uzzaman</h5>
                    <p>PPM-Sheba, Superintendent of Police & President, Khagrachari Police Line High ‍School.
                        khagrachari</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-12">
                <div class="member">
                    <div class="member-pic set-bg"
                         data-setbg="{{ asset('assets/images/committee/Mr_Uttam_Kumar_Nath.jpg') }}">
                    </div>
                    <h5>Mr. Uttam Kumar Nath</h5>
                    <p>Head Teacher, Khagrachari Police Line High School, member of secretary</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-12">
                <div class="member">
                    <div class="member-pic set-bg"
                         data-setbg="{{ asset('assets/images/committee/Mr_Bimbishar_Khisa.jpg') }}">
                    </div>
                    <h5>Mr. Bimbishar Khisa</h5>
                    <p>Head Teacher, Parachara High School. Sadar, Khagrachari, Member</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-12">
                <div class="member">
                    <div class="member-pic set-bg"
                         data-setbg="{{ asset('assets/images/committee/S.M_Elias_Azam.jpg') }}">
                    </div>
                    <h5>S.M Elias Azam</h5>
                    <p> APBN High School, Sadar, Khagrachari, Member</p>
                </div>
            </div>
        </div>
    </div>
@endsection

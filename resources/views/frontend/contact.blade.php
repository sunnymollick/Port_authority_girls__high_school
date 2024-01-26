@extends('frontend.layouts.master')
@section('title', 'Contact Us')
@section('content')
    <div class="container m-top-60">
        <div class="row">
            <div class="section-title text-center">
                <h3>Feel Free to contact us</h3>
                <hr/>
            </div>
            <div class="col-md-12 col-sm-12">
                <div class="map-section">
                    <div class="contact-info-warp">
                        <div class="contact-info">
                            <h4>Address</h4>
                            <p>40 Baria Street 133/2, NewYork City, US</p>
                        </div>
                        <div class="contact-info">
                            <h4>Phone</h4>
                            <p>(+88) 111 555 666</p>
                        </div>
                        <div class="contact-info">
                            <h4>Email</h4>
                            <p>infodeercreative@gmail.com</p>
                        </div>
                        <div class="contact-info">
                            <h4>Working time</h4>
                            <p>Monday - Friday: 08 AM - 06 PM</p>
                        </div>
                    </div>
                    <!-- Google map -->
                    <div class="map">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d548.5407801108852!2d91.83784979388741!3d22.34880260114605!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x30ad276467fc9735%3A0xc77ea17d6dcd7d18!2sw3xplorers+Bangladesh!5e0!3m2!1sbn!2sbd!4v1553075494684"
                            width="98%" height="500" frameborder="0" style="border:0" allowfullscreen></iframe>
                    </div>
                </div>
            </div>
            <div class="col-md-12 col-sm-12">
                <div class="contact-form spad pb-0">
                    <div class="section-title text-center">
                        <h3>We Appreciate your feedback</h3>
                    </div>
                    <form class="comment-form --contact">
                        <div class="col-md-4 col-sm-12">
                            <input type="text" placeholder="Your Name">
                        </div>
                        <div class="col-md-4 col-sm-12">
                            <input type="text" placeholder="Your Email">
                        </div>
                        <div class="col-md-4 col-sm-12">
                            <input type="text" placeholder="Subject">
                        </div>
                        <div class="col-md-12 col-sm-12">
                            <textarea placeholder="Message"></textarea>
                            <div class="text-center">
                                <button class="site-btn">SUBMIT</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

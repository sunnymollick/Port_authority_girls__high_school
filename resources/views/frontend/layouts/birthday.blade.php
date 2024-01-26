<!-- Hero section -->
@if($birthday->count()>0)
    <section class="fact-section spad set-bg" data-setbg="{{ asset('assets/images/birthday.jpg') }}">
        <div class="container">
            <div id="snow"></div>
            <div class="row">
                <div class="section-title text-center">
                    <h2>Happy Birthday To ...</h2>
                    <p>Many many happy returns of the day</p>
                </div>
                @foreach($birthday as $row)
                    <div class="col-md-2 col-sm-12">
                        <div class="member">
                            <div class="member-pic set-bg" data-setbg="{{ asset($row->file_path) }}"
                                 style="max-width: 150px;max-height: 150px;opacity: .80"></div>
                            <h6>{{ $row->std_name }}</h6>
                            <h6>[{{ $row->std_code }}]</h6>
                            <p>Class : {{ $row->class_name }}, Section : {{ $row->section  }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    <!-- Hero section end -->
    <link rel="stylesheet" href="{{ asset('/assets/css/birthday_snow.css') }}">
    <script src="{{ asset('assets/js/birthday_snow.js') }}"></script>
@endif

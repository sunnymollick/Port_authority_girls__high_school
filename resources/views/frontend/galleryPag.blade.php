@if($galleries)
    <div class="col-md-12 col-sm-12">
        @foreach($galleries as $gallery)
            <div class="col-md-3 col-sm-12" style="padding: 5px;">
                <img src="{{ asset($gallery->file_path) }}" data-gallery="first-gallery"
                     alt="{{ $gallery->title }}" class="img-responsive img-thumbnail glryImg"/>
            </div>
        @endforeach
    </div>
@endif


{!! $galleries->render() !!}
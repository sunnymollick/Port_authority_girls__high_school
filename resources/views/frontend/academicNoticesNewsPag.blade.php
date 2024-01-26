<div class="col-md-12">
    <div class="widget">
        @if($notices)
            <div class="recent-post-widget">
                @foreach($notices as $notice)
                    <div class="rp-item">
                        <img class="rp-thumb set-bg" src="{{ asset($notice->file_path) }}"/>
                        <div class="rp-content">
                            <a href="{{ URL :: to('/viewNews/'.$notice->id) }}">
                                <h6>{{ $notice->title }}</h6></a>
                            <p><i class="fa fa-clock-o"></i> {{ $notice->created_at }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

{!! $notices->render() !!}
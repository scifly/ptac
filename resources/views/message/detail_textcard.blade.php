<div class="small-box bg-green">
    <div class="inner">
        <h3>{!! $message->{'title'} !!}</h3>
        <p>{!! $message->{'description'} !!}</p>
    </div>
    <div class="icon">
        <i class="fa fa-link"></i>
    </div>
    <a href="{!! $message->{'url'} !!}" class="small-box-footer" target="_blank">
        {!! $message->{'btntxt'} !!}
        <i class="fa fa-arrow-circle-right"></i>
    </a>
</div>
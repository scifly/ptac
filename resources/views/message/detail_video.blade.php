<dl> 
    <dt>标题: </dt>
    <dd>{!! $message->{'title'} !!}</dd>
    <dt>描述: </dt> 
    <dd>{!! $message->{'description'} !!}</dd>
    <dt>视频: </dt>
    <dd>
        <video height="200" controls> 
            <source src="{!! $message->{'path'} !!}" type="video/mp4">
        </video>
    </dd>
</dl>
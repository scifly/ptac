@foreach ($messages as $message)
    <div class="table-list ">
        <div class="line"></div>
        <div class="teacher-list-box grayline">
            <div class="teacher-work-box">
                <a class="teacher-work-head" style="color: #000;" href="{!! $message->uri !!}">
                    <div class="titleinfo">
                        <div class="titleinfo-head-left fl">
                            <div class="title ml12">{!! $message->title !!}</div>
                            <div class="title-info ml12">
                                {!! ($type == 'sent' ? '接收者' : '发送者') . ': ' . $message->realname !!}
                            </div>
                        </div>
                        <span class="worktime">
                            {!! $message->created !!}
                            @if ($type == 'sent')
                                <span class="info-status {!! $message->color !!}">
                                    {!! $message->status !!}
                                </span>
                            @endif
                        </span>
                    </div>
                </a>
            </div>
        </div>
    </div>
@endforeach
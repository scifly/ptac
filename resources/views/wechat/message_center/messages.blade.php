@foreach ($messages as $message)
    <div class="table-list ">
        <div class="line"></div>
        <div class="teacher-list-box grayline">
            <div class="teacher-work-box">
                <a class="teacher-work-head" style="color: #000;"
                   href="@if ($type == 'sent') {!! $message->sent ? 'mc/show' : 'mc/edit' !!}/{!! $message->id !!} @else mc/show/{!! $message->id !!} @endif"
                >
                    <div class="titleinfo">
                        <div class="titleinfo-head">
                            <div class="titleinfo-head-left fl">
                                <div class="title ml12">{!! $message->title !!}</div>
                                <div class="title-info ml12">{!! $message->realname !!}</div>
                            </div>
                            <span class="worktime">
                                {!! $message->created !!}
                                @if ($type == 'sent')
                                    <span class="info-status {!! $message['sent'] ? 'green' : ($message['event_id'] ? 'orange' : 'red') !!}">
                                        {!! $message['sent'] ? '已发送' : ($message['event_id'] ? '定时' : '草稿') !!}
                                    </span>
                                @endif
                            </span>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
@endforeach
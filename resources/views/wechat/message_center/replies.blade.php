@foreach ($replies as $reply)
    <li class="discuss_item">
        <div>
            <div class="user_info">
                <strong class="nickname">{!! $reply['realname'] !!}</strong>
                <img class="avatar" src="{!! $reply['avatar_url'] !!}" alt="">
                <p class="discuss_extra_info">{!! $reply['replied_at'] !!}</p>
            </div>
            <div class="discuss_message">
                <div class="discuss_message_content">{!! $reply['content'] !!}</div>
                <a class="del-icon-btn" href="javascript:">
                    <span id="{!! $reply['id'] !!}" class="del-icon icon iconfont icon-lajixiang del-reply"></span>
                </a>
            </div>
        </div>
    </li>
@endforeach
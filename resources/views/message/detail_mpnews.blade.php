<div class="box box-widget widget-user-2">
    <div class="widget-user-header bg-yellow">
        <div class="widget-user-image">
            <img class="img-circle"
                 src="{!! $message->{'articles'}[0]->{'image_url'} !!}"
                 alt=""
            />
        </div>
        <h3 class="widget-user-username">
            {!! $message->{'articles'}[0]->{'title'} !!}
        </h3>
        <h5 class="widget-user-desc">
            {!! $message->{'articles'}[0]->{'digest'} !!}
        </h5>
    </div>
    <div class="box-footer no-padding">
        <ul class="nav nav-stacked">
            @for ($i = 1; $i <= sizeof($message->{'articles'}) - 1; $i++)
                <li>
                    <a href="{!! $message->{'articles'}[$i]->{'content_source_url'} !!}">
                        {!! $message->{'articles'}[$i]->{'title'} !!}
                        <img src="{!! $message->{'articles'}[$i]->{'image_url'} !!}"
                             alt="" class="pull-right"
                             style="height: 16px;"
                        />
                    </a>
                </li>
            @endfor
        </ul>
    </div>
</div>
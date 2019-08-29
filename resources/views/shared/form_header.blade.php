<span id="breadcrumb" style="color: #999; font-size: 13px;">{!! $breadcrumb ?? '' !!}</span>
<div class="box-tools pull-right">
    @if (isset($buttons))
        @foreach ($buttons as $button)
            @can('act', $uris[$button['id']])
                {!! Form::button(
                    Html::tag('i', '&nbsp;' . $button['label'], [
                        'class' => $button['icon'] . '&nbsp;' . ($button['color'] ?? 'text-blue')
                    ]),
                    ['id' => $button['id'], 'class' => 'btn btn-box-tool']
                ) !!}
            @endcan
        @endforeach
    @endif
    @if (!isset($disabled))
        @can('act', $uris['index'])
            {!! Form::button(
                Html::tag('i', ' 返回列表', ['class' => 'fa fa-mail-reply text-blue']),
                ['id' => 'record-list', 'class' => 'btn btn-box-tool']
            ) !!}
        @endcan
    @endif
</div>
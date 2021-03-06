<div class="form-group" id="{!! $divId ?? '' !!}" style="{!! $style ?? '' !!}">
    {!! Form::label($id, $label, [
        'class' => ($wl ?? 'col-sm-3') . ' control-label',
    ]) !!}
    <div class="{!! $wr ?? 'col-sm-6' !!}">
        <div class="input-group">
            <div class="input-group-addon">
                {!! Html::tag('i', '', [
                    'style' => 'width: 20px;',
                    'class' => $icon ?? 'fa fa-list'
                ]) !!}
            </div>
            {!! Form::select($id, $items, null, [
                'class' => 'form-control select2',
                'style' => 'width: 100%;',
                'disabled' => $disabled ?? sizeof($items) <= 1
            ]) !!}
        </div>
        @if (isset($help))
            <p class="help-block">{!! $help !!}</p>
        @endif
    </div>
</div>
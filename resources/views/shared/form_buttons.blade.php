@include('shared.form_overlay')
<div class="box-footer">
    <div class="form-group">
        <div class="col-sm-6 col-sm-offset-3">
            {!! Form::button(
                Html::tag('i', $label ?? ' 保存', ['class' => 'fa ' . ($class ?? 'fa-save')]), [
                    'id' => $id ?? null, 'type' => 'submit', 'class' => 'btn btn-primary'
                ]
            ) !!}
            @if (!isset($disabled))
                @can('act', $uris['index'])
                    {!! Form::button(
                        Html::tag('i', ' 取消', ['class' => 'fa fa-mail-reply']), [
                            'id' => 'cancel', 'type' => 'reset',
                            'class' => 'btn btn-default pull-right'
                        ]
                    ) !!}
                @endcan
            @endif
        </div>
    </div>
</div>
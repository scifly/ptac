<div class="form-group">
    {!! Form::label($id, $label, ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-6">
        <div class="input-group">
            <div class="input-group-addon">
                <i class="{!! $icon ?? 'fa fa-list-alt' !!}" style="width: 20px;"></i>
            </div>
            {!! Form::select(
                $id . '[]', $items,
                isset($selectedItems) ? array_keys($selectedItems) : null,
                [
                    'id' => $id,
                    'multiple' => 'multiple',
                    'class' => 'form-control',
                    'style' => 'width: 100%;',
                    isset($required) ? 'required' : '',
                ]
            ) !!}
        </div>
    </div>
</div>
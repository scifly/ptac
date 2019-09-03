<div class="form-group">
    {!! Form::label($id, $label, [
        'class' => 'col-sm-3 control-label'
    ]) !!}
    <div class="col-sm-6">
        <div class="input-group">
            <div class="input-group-addon">
                {!! Html::tag('i', '', [
                    'style' => 'width: 20px;',
                    'class' => $icon ?? 'fa fa-list-alt'
                ]) !!}
            </div>
            {!! Form::select( $id . '[]', $items, $selectedItems, [
                'id' => $id,
                'multiple' => 'multiple',
                'class' => 'form-control',
                'style' => 'width: 100%;',
                isset($required) ? 'required' : '',
            ]) !!}
        </div>
    </div>
</div>
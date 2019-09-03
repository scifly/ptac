<div class="form-group">
    @include('shared.label', ['field' => $id, 'label' => $label])
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
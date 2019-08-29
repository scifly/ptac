<div class="form-group">
    {!! Form::label($field ?? 'remark', $label ?? '备注', [
        'class' => 'col-sm-3 control-label'
    ]) !!}
    <div class="col-sm-6">
        <div class="input-group">
            @include('shared.icon_addon', ['class' => 'fa-comment-o'])
            {!! Form::textarea($field ?? 'remark', null, [
                'id' => $field ?? 'remark',
                'class' => 'form-control text-blue',
                'rows' => 5,
                'placeholder' => $placeholder ?? '(备注)'
            ]) !!}
        </div>
    </div>
</div>
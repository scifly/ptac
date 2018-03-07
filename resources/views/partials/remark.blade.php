<div class="form-group">
    {!! Form::label(!isset($field) ? 'remark' : $field, !isset($label) ? '备注' : $label, [
        'class' => 'col-sm-3 control-label'
    ]) !!}
    <div class="col-sm-6">
        <div class="input-group">
            @include('partials.icon_addon', ['class' => 'fa-comment-o'])
            {!! Form::textarea(!isset($field) ? 'remark' : $field, null, [
                'id' => !isset($field) ? 'remark' : $field,
                'class' => 'form-control text-blue',
            ]) !!}
        </div>
    </div>
</div>
<div class="form-group">
    {!! Form::label(!isset($field) ? 'remark' : $field, !isset($label) ? '备注' : $label, [
        'class' => 'col-sm-3 control-label'
    ]) !!}
    <div class="col-sm-6">
        <div class="input-group">
            <div class="input-group-addon">
                <i class="fa fa-comment-o"></i>
            </div>
            {!! Form::textarea(!isset($field) ? 'remark' : $field, null, [
                'id' => !isset($field) ? 'remark' : $field,
                'class' => 'form-control',
            ]) !!}
        </div>
    </div>
</div>
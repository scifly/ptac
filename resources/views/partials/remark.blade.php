<div class="form-group">
    {!! Form::label('remark', !isset($label) ? '备注' : $label, [
        'class' => 'col-sm-3 control-label'
    ]) !!}
    <div class="col-sm-6">
        <div class="input-group">
            <div class="input-group-addon">
                <i class="fa fa-comment-o"></i>
            </div>
            {!! Form::textarea(!isset($field) ? 'remark' : $field, null, [
                'id' => 'remark',
                'class' => 'form-control',
            ]) !!}
        </div>
    </div>
</div>
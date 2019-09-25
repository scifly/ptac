<div class="form-group">
    {!! Form::label($name = $id ?? 'enabled', $label ?? '状态', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-6" style="padding-top: 5px;">
        {!! Form::radio($name, 1, $value ?? true, ['id' => $name . '1', 'class' => 'minimal']) !!}
        {!! Form::label($name . '1', $options[0] ?? '启用', ['class' => 'switch-lbl']) !!}
        {!! Form::radio($name, 0, !($value ?? true), ['id' => $name . '2', 'class' => 'minimal']) !!}
        {!! Form::label($name . '2', $options[1] ?? '禁用', ['class' => 'switch-lbl']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label($id, $label ?? '状态', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-6" style="padding-top: 5px;">
        {!! Form::radio($id, 1, $value ?? true, ['id' => $id . '1', 'class' => 'minimal']) !!}
        {!! Form::label($id . '1', $options[0] ?? '启用', ['class' => 'switch-lbl']) !!}
        {!! Form::radio($id, 0, !($value ?? true), ['id' => $id . '2', 'class' => 'minimal']) !!}
        {!! Form::label($id . '2', $options[1] ?? '禁用', ['class' => 'switch-lbl']) !!}
    </div>
</div>
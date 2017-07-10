<div class="form-group">
    {!! Form::label('name', '名称') !!}
    <div class="form-controls">
        {!! Form::text('name', null, ['class' => 'form-control']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('address', '地址') !!}
    <div class="form-controls">
        {!! Form::text('address', null, ['class' => 'form-control']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('school_type_id', '类型') !!}
    <div class="form-controls">
        {!! Form::select('school_type_id', $schoolTypes, null, ['class' => 'form-control']) !!}
    </div>
</div>
{!! Form::submit('保存', ['class' => 'btn btn-primary']) !!}
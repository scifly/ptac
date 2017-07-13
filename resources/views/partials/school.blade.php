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
<div class="form-group">
    {!! Form::label('corp_id', '所属企业') !!}
    <div class="form-controls">
        {!! Form::select('corp_id', $corps, null, ['class' => 'form-control']) !!}
    </div>
</div>
<div class="form-group">
    <div class="form-controls">
        {!! Form::radio('enabled', '1', true) !!} {!! Form::label('enabled', '启用') !!}
        {!! Form::radio('enabled', '0') !!} {!! Form::label('enabled', '禁用') !!}
    </div>
</div>
{!! Form::submit('保存', ['class' => 'btn btn-primary']) !!}
<div class="form-horizontal">
    <div class="form-group">
        {!! Form::label('name', '名称', ['class' => 'col-sm-4 control-label']) !!}
        <div class="col-md-2">
            {!! Form::text('name', null, [
            'class' => 'form-control',
            'placeholder' => '学校类型'
            ]) !!}
        </div>
    </div>
    <div class="form-group">
        {!! Form::label('remark'), '备注', ['class' => 'col-sm-4 control-label'] !!}
        <div class="col-md-3">
            {!! Form::text('remark'), null, [
            'class' => 'form-control' ,
            'type' => 'textarea',
            'placeholder' => '备注'
            ] !!}
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-2 col-sm-offset-4">
            {!! Form::radio('enabled', '1', true) !!}
            {!! Form::label('enabled', '启用') !!}
            {!! Form::radio('enabled', '0') !!}
            {!! Form::label('enabled', '禁用') !!}
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-2 col-md-offset-4">
            {!! Form::reset('取消', ['class' => 'btn btn-default pull-left']) !!}
            {!! Form::submit('保存', ['class' => 'btn btn-primary pull-right']) !!}
        </div>
    </div>
</div>
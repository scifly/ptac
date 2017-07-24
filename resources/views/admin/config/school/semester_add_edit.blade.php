<div class="form-horizontal">
    <div class="form-group">
        {!! Form::label('school_id', '所属学校', ['class' => 'col-sm-4 control-label']) !!}
        <div class="col-md-2">
            {!! Form::select('school_id',$schools, null, [ 'class' => 'form-control' ]) !!}
        </div>
    </div>
    <div class="form-group">
        {!! Form::label('name', '学期名称', ['class' => 'col-sm-4 control-label']) !!}
        <div class="col-md-2">
            {!! Form::text('name', null, [
            'class' => 'form-control',
            'placeholder' => '学期名称'
            ]) !!}
        </div>
    </div>
    <div class="form-group">
        {!! Form::label('start_date', '学期开始日期', ['class' => 'col-sm-4 control-label']) !!}
        <div class="col-md-2">
            <div class="input-group date">
                <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                </div>
                {!! Form::text('start_date'), null, ['class' => 'form-control pull-right'] !!}
            </div>
        </div>
    </div>
    <div class="form-group">
        {!! Form::label('end_date'), '学期截止日期', ['class' => 'col-sm-4 control-label'] !!}
        <div class="col-md-2">
            <div class="input-group date">
                <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                </div>
                {!! Form::text('end_date'), null, ['class' => 'form-control pull-right'] !!}
            </div>
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
<div class="box box-primary">
    <div class="box-header"></div>
    <div class="box-body">
        <div class="form-horizontal">
            <div class="form-group">
                {!! Form::label('name', '名称',['class' => 'col-sm-4 control-label']) !!}
                {!! csrf_field() !!}
                <div class="col-sm-2">
                    {!! Form::text('name', null, ['class' => 'form-control']) !!}
                </div>
                <div class="col-sm-5">
                    <p class="form-control-static text-danger">{{ $errors->first('name') }}</p>
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('grade_id', '所属年级',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::select('grade_id', $grades, null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('educator_ids', '年级主任',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::select('educator_ids', $educators, null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-3 col-sm-offset-4">
                    {!! Form::radio('enabled', '1', true) !!}
                    {!! Form::label('enabled', '启用') !!}
                    {!! Form::radio('enabled', '0') !!}
                    {!! Form::label('enabled', '禁用') !!}
                </div>
            </div>

        </div>
    </div>
    <div class="box-footer">
        {{--button--}}
        <div class="form-group">
            <div class="col-sm-3 col-sm-offset-4">
                {!! Form::reset('取消', ['class' => 'btn btn-default pull-left']) !!}
                {!! Form::submit('保存', ['class' => 'btn btn-primary pull-right']) !!}
            </div>
        </div>
    </div>
</div>

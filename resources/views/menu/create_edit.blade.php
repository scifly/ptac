{!! Form::open([
    'method' => 'post',
    'id' => 'formMenu',
    'class' => 'form-horizontal form-borderd',
    'data-parsley-validate' => 'true',
]) !!}
<div class="form-group">
    {!! Form::label('name', '名称',['class' => 'col-md-3 control-label']) !!}
    <div class="col-md-9">
        {!! Form::text('name', null, [
            'class' => 'form-control',
            'placeholder' => '(不超过40个汉字)',
            'data-parsley-required' => 'true',
            'data-parsley-minlength' => '4',
            'data-parsley-maxlength' => '40'
        ]) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('remark', '备注',['class' => 'col-md-3 control-label']) !!}
    <div class="col-md-9">
        {!! Form::text('remark', null, [
            'class' => 'form-control',
            'data-parsley-required' => 'true'
        ]) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('school_id', '所属学校',['class' => 'col-md-3 control-label']) !!}
    <div class="col-md-9">
        {!! Form::select('school_id', $schools, null, ['class' => 'form-control']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('action_id', 'Action',['class' => 'col-md-3 control-label']) !!}
    <div class="col-md-9">
        {!! Form::select('action_id', $actions, null, ['class' => 'form-control']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('enabled', '是否启用', [
        'class' => 'col-md-3 control-label'
    ]) !!}
    <div class="col-md-9" style="margin-top:5px;">
        <input id="enabled" type="checkbox" name="enabled"
               @if(!empty($menu['enabled'])) checked @endif
               data-render="switchery" data-theme="default"
               data-switchery="true" data-classname="switchery switchery-small" />
    </div>
</div>
{!! Form::hidden('id') !!}
<div class="form-group">
    <div class="col-md-9 col-md-offset-3">
        {!! Form::hidden('nodeid', null, ['id' => 'nodeid']) !!}
        {!! Form::submit('保存', ['class' => 'btn btn-primary pull-left', 'id' => 'save']) !!}
        {!! Form::reset('取消', ['class' => 'btn btn-default pull-right', 'id' => 'cancel']) !!}
    </div>
</div>
{!! Form::close() !!}

<div class="box box-primary">
    <div class="box-header"></div>
    <div class="box-body">
        <div class="form-horizontal">
            <div class="form-group">
                {!! Form::label('name', '角色名称',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-3">
                    {!! Form::text('name', null, [
                    'class' => 'form-control',
                    'placeholder' => '不能超过20个汉字',
                    'data-parsley-required' => 'true',
                    'data-parsley-maxlength' => '20',
                    'data-parsley-minlength' => '2',

                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('remark', '备注',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-3">
                    {!! Form::text('remark', null, [
                    'class' => 'form-control',
                    'placeholder' => '不能超过20个汉字',
                    'data-parsley-required' => 'true',
                    'data-parsley-minlength' => '2',
                    'data-parsley-maxlength' => '20'
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                <label for="enabled" class="col-sm-4 control-label">启用</label>
                <div class="col-sm-3" style="padding-top: 5px;">
                    <input id="enabled" name="enabled" type="checkbox" class="form-control js-switch" checked />
                </div>
            </div>
        </div>
    </div>
    <div class="box-footer">
        {{--button--}}
        <div class="form-group">
            <div class="col-sm-3 col-sm-offset-4">
                {!! Form::submit('保存', ['class' => 'btn btn-primary pull-left', 'id' => 'save']) !!}
                {!! Form::reset('取消', ['class' => 'btn btn-default pull-right', 'id' => 'cancel']) !!}
            </div>
        </div>
    </div>
</div>

<div class="box box-widget">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            <div class="form-group">
                {!! Form::label('name', 'Icon类型名称',[
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('name', null, [
                        'class' => 'form-control special-form-control',
                        'placeholder' => '(请输入功能名称)',
                        'data-parsley-required' => 'true',
                        'data-parsley-maxlength' => '80'
                    ]) !!}

                </div>
            </div>
            <div class="form-group">
                {!! Form::label('remark', '备注', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('remark', null, [
                        'class' => 'form-control special-form-control',
                        'placeholder' => '(请输入备注)',
                        'data-parsley-required' => 'true',
                        'data-parsley-maxlength' => '255'
                    ]) !!}
                </div>
            </div>
            {{--<div class="form-group">--}}
                {{--<label for="enabled" class="col-sm-3 control-label">是否启用</label>--}}
                {{--<div class="col-sm-6" style="margin-top: 5px;">--}}
                    {{--<input id="enabled" type="checkbox" name="enabled" data-render="switchery"--}}
                           {{--data-theme="default" data-switchery="true"--}}
                           {{--@if(!empty($iconType['enabled'])) checked @endif--}}
                           {{--data-classname="switchery switchery-small"/>--}}
                {{--</div>--}}
            {{--</div>--}}
            @include('partials.enabled', ['enabled' => $iconType['enabled']])
        </div>
    </div>
    {{--<div class="box-footer">--}}
    {{--button--}}
    {{--<div class="form-group">--}}
    {{--<div class="col-sm-3 col-sm-offset-3">--}}
    {{--{!! Form::submit('保存', ['class' => 'btn btn-primary pull-left','id' =>'save']) !!}--}}
    {{--{!! Form::reset('取消', ['class' => 'btn btn-default pull-right','id' =>'cancel']) !!}--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--</div>--}}
    @include('partials.form_buttons')
</div>

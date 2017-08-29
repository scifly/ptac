<div class="box box-widget">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($action['id']))
                {{ Form::hidden('id', null, ['id' => 'id', 'value' => $action['id']]) }}
            @endif
            <div class="form-group">
                {!! Form::label('name', 'Action名称', [
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
                {!! Form::label('method', '方法名称', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('method', null, [
                        'class' => 'form-control special-form-control',
                        'placeholder' => '(请输入方法名称)',
                        'data-parsley-required' => 'true',
                        'data-parsley-maxlength' => '255',
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('route', '路由', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('route', null, [
                        'class' => 'form-control special-form-control',
                        'placeholder' => '(请输入路由)',
                        'data-parsley-required' => 'true',
                        'data-parsley-maxlength' => '255',
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('controller', '控制器名称',[
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('controller', null, [
                        'class' => 'form-control  special-form-control',
                        'placeholder' => '(请输入控制器名称)',
                        'data-parsley-required' => 'true',
                        'data-parsley-maxlength' => '255'
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('view', 'view路径', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('view', null, [
                        'class' => 'form-control special-form-control',
                        'placeholder' => '(请输入view路径)',
                        'data-parsley-maxlength' => '255'
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('js', 'js文件路径', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('js', null, [
                        'class' => 'form-control special-form-control',
                        'placeholder' => '(请输入js文件路径)',
                        'data-parsley-maxlength' => '255'
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
                        'data-parsley-maxlength' => '255'
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('action_type_ids', 'HTTP请求类型', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-9">
                    <!--suppress HtmlFormInputWithoutLabel -->
                    <select name="action_type_ids[]" id="action_type_ids" multiple class="col-sm-3">
                        @foreach($actionTypes as $key => $value)
                            @if(isset($selectedActionTypes))
                                <option value="{{$key}}"
                                        @if(array_key_exists($key, $selectedActionTypes))
                                        selected
                                        @endif
                                >
                                    {{$value}}
                                </option>
                            @else
                                <option value="{{$key}}">{{$value}}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>
            {{--<div class="form-group">--}}
                {{--<label for="enabled" class="col-sm-3 control-label">--}}
                    {{--是否启用--}}
                {{--</label>--}}
                {{--<div class="col-sm-6" style="margin-top: 5px;">--}}
                    {{--<input id="enabled" type="checkbox" name="enabled" data-render="switchery"--}}
                           {{--data-theme="default" data-switchery="true"--}}
                           {{--@if(!empty($action['enabled'])) checked @endif--}}
                           {{--data-classname="switchery switchery-small"/>--}}
                {{--</div>--}}
            {{--</div>--}}
            @include('partials.enabled', ['enabled' => $action['enabled']])
        </div>
    </div>
    @include('partials.form_buttons')
</div>
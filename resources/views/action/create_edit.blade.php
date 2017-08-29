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
            @include('partials.multiple_select', [
                'label' => 'HTTP请求类型',
                'for' => 'action_type_ids',
                'items' => $actionTypes,
                'selectedItems' => isset($selectedActionTypes) ? $selectedActionTypes : []
            ])
            @include('partials.enabled', ['enabled' => $action['enabled']])
        </div>
    </div>
    @include('partials.form_buttons')
</div>
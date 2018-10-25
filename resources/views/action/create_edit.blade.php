<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('shared.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($action['id']))
                {{ Form::hidden('id', $action['id'], ['id' => 'id']) }}
            @endif
            <div class="form-group">
                {!! Form::label('name', 'Action名称', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('name', null, [
                        'class' => 'form-control text-blue',
                        'placeholder' => '(请输入功能名称)',
                        'required' => 'true',
                        'maxlength' => '80'
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('method', '方法名称', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('method', null, [
                        'class' => 'form-control text-blue',
                        'placeholder' => '(请输入方法名称)',
                        'required' => 'true',
                        'maxlength' => '255',
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('route', '路由', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('route', null, [
                        'class' => 'form-control text-blue',
                        'placeholder' => '(请输入路由)',
                        'required' => 'true',
                        'maxlength' => '255',
                    ]) !!}
                </div>
            </div>
            @include('shared.single_select', [
                'label' => '控制器',
                'id' => 'tab_id',
                'items' => $tabs,
                'icon' => 'fa fa-building text-blue'
            ])
            <div class="form-group">
                {!! Form::label('view', 'view路径', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('view', null, [
                        'class' => 'form-control text-blue',
                        'placeholder' => '(请输入view路径)',
                        'maxlength' => '255'
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('js', 'js文件路径', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('js', null, [
                        'class' => 'form-control text-blue',
                        'placeholder' => '(请输入js文件路径)',
                        'maxlength' => '255'
                    ]) !!}
                </div>
            </div>
            @include('shared.multiple_select', [
                'label' => 'HTTP请求类型',
                'id' => 'action_type_ids',
                'items' => $actionTypes,
                'selectedItems' => $selectedActionTypes ?? null
            ])
            @include('shared.remark')
            @include('shared.switch', [
                'id' => 'enabled',
                'value' => $action['enabled'] ?? null
            ])
        </div>
    </div>
    @include('shared.form_buttons')
</div>
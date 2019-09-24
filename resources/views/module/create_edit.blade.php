<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('shared.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
        @if (!empty($module['id']))
            {{ Form::hidden('id', $module['id']) }}
        @endif
        <!-- 名称 -->
            <div class="form-group">
                {!! Form::label('name', '名称', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group" style="width: 100%;">
                        <div class="input-group-addon" style="width: 45px;">
                            <strong>名</strong>
                        </div>
                        {!! Form::text('name', null, [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(不得超过6个汉字)',
                            'required' => 'true',
                            'data-parsley-length' => '[2, 6]'
                        ]) !!}
                    </div>
                </div>
            </div>
            <!-- 所属学校 -->
            @include('shared.single_select', [
                'label' => '所属学校',
                'id' => 'school_id',
                'icon' => 'fa fa-university text-purple',
                'items' => $schools
            ])
            @include('shared.single_select', [
                'label' => '所属角色',
                'id' => 'group_id',
                'icon' => 'fa fa-meh-o',
                'items' => $groups
            ])
            <!-- 控制器 -->
            @include('shared.single_select', [
                'label' => '控制器',
                'id' => 'tab_id',
                'icon' => 'fa fa-folder-o',
                'items' => $tabs
            ])
            <!-- uri -->
            <div class="form-group">
                {!! Form::label('uri', 'uri', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group" style="width: 100%;">
                        @include('shared.icon_addon', ['class' => 'fa fa-link'])
                        {!! Form::text('uri', null, [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(请输入链接地址，可选)',
                            'type' => 'url',
                            'maxlength' => '255'
                        ]) !!}
                    </div>
                </div>
            </div>
            <!-- 模块图片 -->
            <div class="form-group">
                {!! Form::label('media_id', '模块图片', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="preview">
                        {!! Form::hidden('media_id', isset($media) ? $media->id : null, [
                            'id' => 'media_id'
                        ]) !!}
                        @if (isset($media))
                            <img src="../../{!! $media->path !!}" alt="">
                        @endif
                    </div>
                    <label for="file-image" class="custom-file-upload text-blue">
                        <i class="fa fa-cloud-upload"></i> 上传图片
                    </label>
                    {!! Form::file('file-image', [
                        'id' => 'file-image',
                        'accept' => 'image/*',
                        'class' => 'file-upload',
                    ]) !!}
                </div>
            </div>
            <!-- 排序 -->
            <div class="form-group">
                {!! Form::label('order', '排序', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group" style="width: 100%;">
                        @include('shared.icon_addon', ['class' => 'fa fa-sort-numeric-asc'])
                        {!! Form::number('order', null, [
                            'class' => 'form-control text-blue',
                            'type' => 'number',
                            'placeholder' => '(请输入一个整数，值越大，排名越靠后)',
                            'required' => 'true'
                        ]) !!}
                    </div>
                </div>
            </div>
            @include('shared.remark')
            @include('shared.switch', [
                'id' => 'isfree',
                'label' => '类型',
                'value' => $modules['isfree'] ?? null,
                'options' => ['基本', '增值']
            ])
            @include('shared.switch', [
                'id' => 'enabled',
                'value' => $module['enabled'] ?? null
            ])
        </div>
    </div>
    @include('shared.form_buttons')
</div>
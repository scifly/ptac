<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($menu['id']))
                {{ Form::hidden('id', $menu['id'], ['id' => 'id']) }}
            @endif
            @if (!empty($menu['position']))
                {{ Form::hidden('position', $menu['position'], ['id' => 'position']) }}
            @endif
            {{ Form::hidden('parent_id', isset($parentId) ? $parentId : null, ['id' => 'parent_id']) }}
            <!-- 名称 -->
            <div class="form-group">
                {!! Form::label('name', '名称', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-list-ul"></i>
                        </div>
                        {!! Form::text('name', null, [
                            'id' => 'name',
                            'class' => 'form-control',
                            'placeholder' => '(不得超过8个汉字)',
                            'required' => 'true',
                            'data-parsley-length' => '[2, 8]'
                        ]) !!}
                    </div>
                </div>
            </div>
            <!-- 菜单类型ID -->
            {{ Form::hidden('menu_type_id', isset($menuTypeId) ? $menuTypeId : null, [
                'id' => 'menu_type_id'
            ]) }}
            <!-- 图标ID -->
            <div class="form-group">
                {!! Form::label('icon_id', '图标', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-fonticons"></i>
                        </div>
                        {{ Form::select('icon_id', $icons, null, [
                            'id' => 'icon_id',
                            'style' => 'width: 100%;'
                        ]) }}
                    </div>
                </div>
            </div>
            <!-- 链接地址 -->
            <div class="form-group">
                {!! Form::label('name', '链接地址', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-link"></i>
                        </div>
                        {!! Form::text('uri', null, [
                            'id' => 'uri',
                            'class' => 'form-control',
                            'placeholder' => '(可选)',
                            'data-parsley-length' => '[1, 255]'
                        ]) !!}
                    </div>
                </div>
            </div>
            <!-- 包含的卡片 -->
            @include('partials.multiple_select', [
                'label' => '包含卡片',
                'id' => 'tab_ids',
                'icon' => 'fa fa-calendar-check-o',
                'items' => $tabs,
                'selectedItems' => isset($selectedTabs) ? $selectedTabs : NULL
            ])
            <!-- 备注 -->
            @include('partials.remark')
            <!-- 状态 -->
            @include('partials.enabled', [
                'id' => 'enabled',
                'value' => isset($menu['enabled']) ? $menu['enabled'] : NULL
            ])
        </div>
    </div>
    @include('partials.form_buttons')
</div>
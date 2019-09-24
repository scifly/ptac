<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('shared.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (isset($menu))
                {{ Form::hidden('id', $menu['id']) }}
                {{ Form::hidden('position', $menu['position'], ['id' => 'position']) }}
            @endif
            {{ Form::hidden('parent_id', $parentId ?? null, ['id' => 'parent_id']) }}
            <!-- 名称 -->
            <div class="form-group">
                @include('shared.label', ['field' => 'name', 'label' => '名称'])
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('shared.icon_addon', ['class' => 'fa-list-ul'])
                        {!! Form::text('name', null, [
                            'id' => 'name',
                            'class' => 'form-control text-blue',
                            'placeholder' => '(不得超过8个汉字)',
                            'required' => 'true',
                            'data-parsley-length' => '[2, 8]'
                        ]) !!}
                    </div>
                </div>
            </div>
            <!-- 图标ID -->
            @include('shared.single_select', [
                'id' => 'icon_id',
                'label' => '图标',
                'items' => $icons,
                'icon' => 'fa fa-fonticons'
            ])
            <!-- 链接地址 -->
            <div class="form-group">
                @include('shared.label', ['field' => 'uri', 'label' => '链接地址'])
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('shared.icon_addon', ['class' => 'fa-link'])
                        {!! Form::text('uri', null, [
                            'id' => 'uri',
                            'class' => 'form-control text-blue',
                            'placeholder' => '(可选)',
                            'data-parsley-length' => '[1, 255]'
                        ]) !!}
                    </div>
                </div>
            </div>
            <!-- 包含的卡片 -->
            @include('shared.multiple_select', [
                'label' => '包含卡片',
                'id' => 'tab_ids',
                'icon' => 'fa fa-calendar-check-o',
                'items' => $tabs,
                'selectedItems' => $selectedTabs
            ])
            <!-- 备注 -->
            @include('shared.remark')
            <!-- 状态 -->
            @include('shared.switch', [
                'id' => 'enabled',
                'value' => $menu['enabled'] ?? null
            ])
        </div>
    </div>
    @include('shared.form_buttons')
</div>
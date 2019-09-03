<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('shared.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($tab['id']))
                {!! Form::hidden('id', $tab['id'], ['id' => 'id']) !!}
            @endif
            <div class="form-group">
                @include('shared.label', ['field' => 'name', 'label' => '名称'])
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('shared.icon_addon', ['class' => 'fa-folder-o'])
                        {!! Form::text('name', null, [
                            'class' => 'form-control text-blue'
                        ]) !!}
                    </div>
                </div>
            </div>
            @include('shared.single_select', [
                'label' => '所属角色',
                'id' => 'group_id',
                'items' => $groups,
                'icon' => 'fa fa-meh-o'
            ])
            <div class="form-group">
                @include('shared.label', ['field' => 'icon_id', 'label' => '图标'])
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('shared.icon_addon', ['class' => 'fa-fonticons'])
                        {!! Form::select('icon_id', $icons, null, [
                            'id' => 'icon_id',
                            'style' => 'width: 100%;'
                        ]) !!}
                    </div>
                </div>
            </div>
            @include('shared.single_select', [
                'label' => '默认Action',
                'id' => 'action_id',
                'items' => $actions
            ])
            @include('shared.multiple_select', [
                'label' => '所属菜单',
                'id' => 'menu_ids',
                'items' => $menus,
                'selectedItems' => $selectedMenus
            ])
            @include('shared.remark')
            @include('shared.switch', [
                'id' => 'enabled',
                'value' => $tab['enabled'] ?? null
            ])
        </div>
    </div>
    @include('shared.form_buttons')
</div>
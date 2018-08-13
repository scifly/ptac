<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($tab['id']))
                {{ Form::hidden('id', $tab['id'], ['id' => 'id']) }}
            @endif
            <div class="form-group">
                {!! Form::label('name', '名称', [
                    'class' => 'col-sm-3 control-label',
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('name', null, [
                        'class' => 'form-control text-blue'
                    ]) !!}
                </div>
            </div>
            @include('partials.single_select', [
                'label' => '所属角色',
                'id' => 'group_id',
                'items' => $groups,
                'icon' => 'fa fa-meh-o'
            ])
            <div class="form-group">
                {!! Form::label('icon_id', '图标', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('partials.icon_addon', ['class' => 'fa-fonticons'])
                        {{ Form::select('icon_id', $icons, null, [
                            'id' => 'icon_id',
                            'style' => 'width: 100%;'
                        ]) }}
                    </div>
                </div>
            </div>
            @include('partials.single_select', [
                'label' => '默认Action',
                'id' => 'action_id',
                'items' => $actions
            ])
            @include('partials.multiple_select', [
                'label' => '所属菜单',
                'id' => 'menu_ids',
                'items' => $menus,
                'selectedItems' => $selectedMenus ?? null
            ])
            @include('partials.remark')
            @include('partials.enabled', [
                'id' => 'enabled',
                'value' => $tab['enabled'] ?? null
            ])
        </div>
    </div>
    @include('partials.form_buttons')
</div>
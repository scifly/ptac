<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('shared.form_header')
    </div>
    <div class="box-body">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs pull-right">
                <li><a href="#tab03" data-toggle="tab">控制器/功能权限</a></li>
                <li><a href="#tab02" data-toggle="tab">菜单权限</a></li>
                <li class="active"><a href="#tab01" data-toggle="tab">基本信息</a></li>
                <li class="pull-left header"><i class="fa fa-th"></i>角色</li>
            </ul>
            <div class="tab-content">
                <!-- 角色基本信息 -->
                <div class="tab-pane active" id="tab01">
                    <div class="form-horizontal">
                        <!-- 角色ID -->
                        @if (isset($group))
                            {!! Form::hidden('id', $group['id']) !!}
                        @endif
                        {!! Form::hidden('menu_ids', join(',', $selectedMenuIds ?? [])) !!}
                        {!! Form::hidden('tab_ids', join(',', $selectedTabIds ?? [])) !!}
                        {!! Form::hidden('action_ids', join(',', $selectedActionIds ?? [])) !!}
                        <!-- 角色名称 -->
                        <div class="form-group">
                            @include('shared.label', ['field' => 'name', 'label' => '名称'])
                            <div class="col-sm-6">
                                <div class="input-group">
                                    @include('shared.icon_addon', ['class' => 'fa-meh-o'])
                                    {!! Form::text('name', null, [
                                        'class' => 'form-control text-blue',
                                        'placeholder' => '(不得超过20个汉字)',
                                        'required' => 'true',
                                        'data-parsley-length' => '[2, 20]'
                                    ]) !!}
                                </div>
                            </div>
                        </div>
                        <!-- 角色所属学校 -->
                        @include('shared.single_select', [
                            'id' => 'school_id',
                            'label' => '所属学校',
                            'icon' => 'fa fa-university text-purple',
                            'items' => $schools
                        ])
                        <!-- 角色备注 -->
                        @include('shared.remark')
                        <!-- 状态 -->
                        @include('shared.switch', [
                            'id' => 'enabled',
                            'value' => $group['enabled'] ?? null
                        ])
                    </div>
                </div>
                <!-- 角色菜单权限 -->
                <div class="tab-pane" id="tab02">
                    <div id="menu_tree" class="form-inline"></div>
                </div>
                <!-- 角色控制器/功能权限 -->
                <div class="tab-pane" id="tab03">
                    <div class="row">
                        @foreach ($tabActions as $ta)
                            <div class="col-md-3">
                                <div class="box box-default collapsed-box">
                                    <div class="box-header with-border">
                                        {!! Form::label(
                                            $name = 'tabs[' . ($value = $ta['tab']['id']) . '][enabled]',
                                            Form::checkbox(
                                                $name, $value, in_array($value, $selectedTabIds ?? []),
                                                ['id' => 'tabs[]', 'class' => 'minimal tabs']
                                            )->toHtml() . 
                                            Html::tag('span', $ta['tab']['name'], ['class' => 'plbl'])->toHtml(),
                                            ['class' => 'tabsgroup'], false
                                        ) !!}
                                        <div class="box-tools pull-right">
                                            {!! Form::button(
                                                Html::tag('i', '', ['class' => 'fa fa-plus']),
                                                ['class' => 'btn btn-box-tool', 'data-widget' => 'collapse']
                                            ) !!}
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <ul class="nav nav-stacked">
                                            @foreach($ta['actions'] as $action)
                                                <li>
                                                    <p class="help-block">
                                                        {!! Form::label(
                                                            $name = $id = 'actions[' . ($actionId = $action['id']) . '][enabled]',
                                                            Form::checkbox($name, $actionId, in_array(
                                                                $actionId, $selectedActionIds ?? []), [
                                                                    'id' => $id,
                                                                    'class' => 'minimal actions',
                                                                    'data-method' => $action['method']
                                                            ])->toHtml() .
                                                            Html::tag('span', $action['name'], ['class' => 'plbl'])->toHtml(),
                                                            ['class' => 'actionsgroup'], false
                                                        ) !!}
                                                    </p>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('shared.form_buttons')
</div>
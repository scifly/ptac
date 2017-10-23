<div class="box box-widget">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs pull-right">
                <li><a href="#tab03" data-toggle="tab">卡片/功能权限</a></li>
                <li><a href="#tab02" data-toggle="tab">菜单权限</a></li>
                <li class="active"><a href="#tab01" data-toggle="tab">基本信息</a></li>
                <li class="pull-left header"><i class="fa fa-th"></i>角色</li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="tab01">
                    <div class="form-horizontal">
                        @if (!empty($group['id']))
                            {{ Form::hidden('id', $group['id'], ['id' => 'id']) }}
                        @endif
                        {{ Form::hidden('menu_ids', isset($selectedMenuIds) ? $selectedMenuIds : null, [
                            'id' => 'menu_ids'
                        ]) }}
                        <div class="form-group">
                            {!! Form::label('name', '角色名称', [
                                'class' => 'col-sm-3 control-label'
                            ]) !!}
                            <div class="col-sm-6">
                                {!! Form::text('name', null, [
                                    'class' => 'form-control',
                                    'placeholder' => '(不得超过20个汉字)',
                                    'required' => 'true',
                                    'data-parsley-length' => '[2, 20]'
                                ]) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="school_id" class="col-sm-3 control-label">所属学校</label>
                            <div class="col-sm-6">
                                @if(!isset($group))
                                <select name="school_id" class="form-control menu" id="school_id" style="width: 100%">
                                    @foreach($schools as $key => $value)
                                        <option value="{{ $value }}" >{{ $key }}</option>
                                    @endforeach
                                </select>
                                @else
                                    {!! Form::hidden('school_id', $group['school_id'], ['id' => 'school_id']) !!}
                                    <label class="control-label" style="font-weight: normal;">{!! $group->school->name !!}</label>
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('remark', '备注', [
                                'class' => 'col-sm-3 control-label'
                            ]) !!}
                            <div class="col-sm-6">
                                {!! Form::text('remark', null, [
                                    'class' => 'form-control',
                                    'placeholder' => '(不得超过255个汉字)',
                                    'required' => 'true',
                                    'data-parsley-length' => '[2, 255]'
                                ]) !!}
                            </div>
                        </div>
                        @include('partials.enabled', [
                            'label' => '是否启用',
                            'id' => 'enabled',
                            'value' => isset($group['enabled']) ? $group['enabled'] : NULL
                        ])
                    </div>
                </div>
                <div class="tab-pane" id="tab02">
                    <div id="menu_tree" class="form-inline"></div>
                </div>
                <div class="tab-pane" id="tab03">
                    <div class="row">
                    @foreach ($tabActions as $tabAction)
                        <div class="col-md-3">
                            <div class="box box-default collapsed-box">
                                <div class="box-header with-border">
                                    <label for="tabs[{{ $tabAction['tab']['id'] }}]['enabled']" class="tabsgroup">
                                        <input name="tabs[{{ $tabAction['tab']['id'] }}]['enabled']"
                                               id="tabs[]" type="checkbox" class="minimal tabs"
                                               @if(isset($selectedTabs) && in_array($tabAction['tab']['id'], $selectedTabs))
                                                   checked
                                               @endif
                                        >&nbsp;<span style="margin-left: 5px;">{{ $tabAction['tab']['name'] }}</span>
                                    </label>
                                    <div class="box-tools pull-right">
                                        <button type="button" class="btn btn-box-tool" data-widget="collapse">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="box-body">
                                    <ul class="nav nav-stacked">
                                        @foreach($tabAction['actions'] as $action)
                                            <li>
                                                <p class="help-block">
                                                    <label for="actions[{{ $action['id'] }}]['enabled']"></label>
                                                    <input name="actions[{{ $action['id'] }}]['enabled']"
                                                           id="actions[{{ $action['id'] }}]['enabled']"
                                                           type="checkbox" class="minimal actions"
                                                           data-method="{{ $action['method'] }}"
                                                           @if(isset($selectedActions) && in_array($action['id'], $selectedActions))
                                                               checked
                                                           @endif
                                                    >&nbsp;<span>{{ $action['name'] }}</span>
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
    @include('partials.form_buttons')
</div>

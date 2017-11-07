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
            <div class="form-group">
                {!! Form::label('name', '名称',[
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('name', null, [
                        'id' => 'name',
                        'class' => 'form-control',
                        'placeholder' => '(不得超过8个汉字)',
                        'required' => 'true',
                        'data-parsley-length' => '[2, 8]'
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('remark', '备注',[
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('remark', null, [
                        'id' => 'remark',
                        'class' => 'form-control',
                        'required' => 'true'
                    ]) !!}
                </div>
            </div>
            {{ Form::hidden('menu_type_id', isset($menuTypeId) ? $menuTypeId : null, ['id' => 'menu_type_id']) }}
            <div class="form-group">
                {!! Form::label('icon_id', '图标', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6"
                     style="overflow-y: scroll; height: 200px; border: 1px solid gray; margin-left: 15px; width: 393px;">
                    @foreach($icons as $group => $_icons)
                        @foreach ($_icons as $key => $value)
                            <label for="icon_id">
                                <input id="icon_id" type="radio" name="icon_id"
                                       value="{{ $key }}" class="minimal"
                                       @if(isset($menu) && $menu['icon_id'] == $key)
                                       checked
                                        @endif
                                >
                            </label>
                            <i class="{{ $value }}" style="margin-left: 10px;">&nbsp; {{ $value }}</i><br/>
                        @endforeach
                    @endforeach
                </div>
            </div>
            @include('partials.multiple_select', [
                'label' => '包含卡片',
                'id' => 'tab_ids',
                'items' => $tabs,
                'selectedItems' => isset($selectedTabs) ? $selectedTabs : NULL
            ])
            @include('partials.enabled', [
                'id' => 'enabled',
                'value' => isset($menu['enabled']) ? $menu['enabled'] : NULL
            ])
        </div>
    </div>
    @include('partials.form_buttons')
</div>
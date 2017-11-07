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
                {!! Form::label('name', '卡片名称', [
                    'class' => 'col-sm-3 control-label',
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('name', null, [
                        'readonly' => true,
                        'class' => 'form-control'
                    ]) !!}

                </div>
            </div>
            <div class="form-group">
                {!! Form::label('remark', '备注', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('remark', null, [
                        'class' => 'form-control',
                        'placeholder' => '(请输入备注)',
                        'required' => 'true',
                        'maxlength' => '255'
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                <label for="icon_id" class="col-sm-3 control-label">
                    图标
                </label>
                <div class="col-sm-6"
                     style="
                        overflow-y: scroll;
                        height: 200px;
                        border: 1px solid gray;
                        margin-left: 15px;
                        width: 393px;
                    "
                >
                    @foreach($icons as $group => $_icons)
                        @foreach ($_icons as $key => $value)
                            <label for="icon_id">
                                <input id="icon_id" type="radio" name="icon_id"
                                       value="{{ $key }}" class="minimal"
                                       @if(isset($tab) && $tab['icon_id'] == $key)
                                       checked
                                        @endif
                                >
                            </label>
                            <i class="{{ $value }}" style="margin-left: 10px;">&nbsp; {{ $value }}</i><br/>
                        @endforeach
                    @endforeach

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
                'selectedItems' => isset($selectedMenus) ? $selectedMenus : NULL
            ])
            @include('partials.enabled', [
                'id' => 'enabled',
                'value' => isset($tab['enabled']) ? $tab['enabled'] : NULL
            ])
        </div>
    </div>
    @include('partials.form_buttons')
</div>
<div class="box box-widget">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($tab['id']))
                {{ Form::hidden('id', null, ['id' => 'id', 'value' => $tab['id']]) }}
            @endif
            <div class="form-group">
                {!! Form::label('name', '卡片名称',[
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('name', null, [
                        'class' => 'form-control special-form-control',
                        'placeholder' => '(请输入卡片名称)',
                        'data-parsley-required' => 'true',
                        'data-parsley-maxlength' => '80'
                    ]) !!}

                </div>
            </div>
            <div class="form-group">
                {!! Form::label('remark', '备注', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('remark', null, [
                        'class' => 'form-control special-form-control',
                        'placeholder' => '(请输入备注)',
                        'data-parsley-required' => 'true',
                        'data-parsley-maxlength' => '255'
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
                            <i class="{{ $value }}" style="margin-left: 10px;">&nbsp; {{ $value }}</i><br />
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
                'for' => 'menu_ids',
                'items' => $menus,
                'selectedItems' => $selectedMenus
            ]);
            @include('partials.enabled', ['enabled' => $tab['enabled']])
        </div>
    </div>
    @include('partials.form_buttons')
</div>
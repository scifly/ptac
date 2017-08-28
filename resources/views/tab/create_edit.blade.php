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
            <div class="form-group">
                {!! Form::label('action_id', '默认Action', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::select('action_id', $actions, null, [
                        'id' => 'action_id',
                        'style' => 'width: 100%;'
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                <label for="menu_ids" class="col-sm-3 control-label">所属菜单</label>
                <div class="col-sm-6">
                    <select multiple name="menu_ids[]" id="menu_ids" style="width: 100%;">
                        @foreach ($menus as $key => $value)
                            @if(isset($selectedMenus))
                                <option value="{{ $key }}"
                                    @if(array_key_exists($key, $selectedMenus))
                                        selected
                                    @endif
                                >
                                    {{ $value }}
                                </option>
                            @else
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>
            {{--<div class="form-group">--}}
                {{--<label for="enabled" class="col-sm-3 control-label">--}}
                    {{--是否启用--}}
                {{--</label>--}}
                {{--<div class="col-sm-6" style="margin-top: 5px;">--}}
                    {{--<input id="enabled" type="checkbox" name="enabled" data-render="switchery"--}}
                           {{--data-theme="default" data-switchery="true"--}}
                           {{--@if(!empty($tab['enabled'])) checked @endif--}}
                           {{--data-classname="switchery switchery-small"/>--}}
                {{--</div>--}}
            {{--</div>--}}
            @include('partials.enabled', ['enabled' => $tab['enabled']])
        </div>
    </div>
    @include('partials.form_buttons')
</div>
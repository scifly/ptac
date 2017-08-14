<div class="form-group">
    {!! Form::label('name', '名称',[
        'class' => 'col-sm-3 control-label'
    ]) !!}
    <div class="col-sm-6">
        {!! Form::text('name', null, [
            'id' => 'name',
            'class' => 'form-control',
            'placeholder' => '(不超过40个汉字)',
            'data-parsley-required' => 'true',
            'data-parsley-minlength' => '2',
            'data-parsley-maxlength' => '40'
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
            'data-parsley-required' => 'true'
        ]) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('school_id', '所属学校',[
        'class' => 'col-sm-3 control-label'
    ]) !!}
    <div class="col-sm-6">
        {!! Form::select('school_id', $schools, null, [
            'id' => 'school_id',
            'style' => 'width: 100%;'
        ]) !!}
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
    {!! Form::label('icon_id', '图标', [
        'class' => 'col-sm-3 control-label'
    ]) !!}
    <div class="col-sm-6" style="overflow-y: scroll; height: 200px; border: 1px solid gray; margin-left: 15px; width: 393px;">
        {{--{!! Form::select('icon_id', $icons, null, [
            'id' => 'icon_id',
            'style' => 'width: 100%;'
        ]) !!}--}}
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
                <i class="{{ $value }}" style="margin-left: 10px;">&nbsp; {{ $value }}</i><br />
            @endforeach
        @endforeach
    </div>
</div>
<div class="form-group">
    <label for="tab_ids" class="col-sm-3 control-label">包含卡片</label>
    <div class="col-sm-6">
        <select multiple name="tab_ids[]" id="tab_ids" style="width: 100%;">
            @foreach ($tabs as $key => $value)
                @if(isset($selectedTabs))
                    <option value="{{ $key }}" @if(array_key_exists($key, $selectedTabs)) selected @endif>
                        {{ $value }}
                    </option>
                @else
                    <option value="{{ $key }}">{{ $value }}</option>
                @endif
            @endforeach
        </select>
    </div>
</div>
<div class="form-group">
    <label for="enabled" class="col-sm-3 control-label">是否启用</label>
    <div class="col-sm-6" style="margin-top:5px;">
        <input id="enabled" type="checkbox" name="enabled"
               @if(!empty($menu['enabled'])) checked @endif
               data-render="switchery" data-theme="default"
               data-switchery="true" data-classname="switchery switchery-small" />
    </div>
</div>
{!! Form::hidden('id') !!}
<div class="form-group">
    <div class="col-sm-6 col-sm-offset-3">
        {!! Form::hidden('nodeid', null, ['id' => 'nodeid']) !!}
        {!! Form::hidden('parent_id', null, ['id' => 'parent_id']) !!}
        {!! Form::submit('保存', ['class' => 'btn btn-primary pull-left', 'id' => 'save']) !!}
        {!! Form::reset('取消', ['class' => 'btn btn-default pull-right', 'id' => 'cancel']) !!}
    </div>
</div>
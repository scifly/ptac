<div class="form-group">
{{--    <label for="{!! $id !!}" class="col-sm-3 control-label">--}}
{{--        {!! $label ?? '状态' !!}--}}
{{--    </label>--}}
    {!! Form::label($id, $label ?? '状态', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-6" style="padding-top: 5px;">
        <input id="{!! $id !!}1"
               @if(!isset($value) || (isset($value) and $value)) checked @endif
               type="radio" name="{!! $id !!}" class="minimal" value="1">
{{--        <label for="{!! $id !!}1" class="switch-lbl">--}}
{{--            {!! $options[0] ?? '启用' !!}--}}
{{--        </label>--}}
        {!! Form::label($id . '1', $options[0] ?? '启用', ['class' => 'switch-lbl']) !!}
        <input id="{!! $id !!}2" @if (isset($value) && !$value) checked @endif
               type="radio" name="{!! $id !!}" class="minimal" value="0">
{{--        <label for="{!! $id !!}2" class="switch-lbl">--}}
{{--            {!! $options[1] ?? '禁用' !!}--}}
{{--        </label>--}}
        {!! Form::label($id . '2', $options[1] ?? '禁用', ['class' => 'switch-lbl']) !!}
    </div>
</div>
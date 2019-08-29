<div class="form-group">
    {!! Form::label($id, $label ?? '状态', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-6" style="padding-top: 5px;">
{{--        <input id="{!! $id !!}1"--}}
{{--               @if(!isset($value) || (isset($value) and $value)) checked @endif--}}
{{--               type="radio" name="{!! $id !!}" class="minimal" value="1">--}}
        {!! Form::radio($id, 1, !isset($value) || isset($value) && $value, [
            'id' => $id . '1', 'class' => 'minimal'
        ]) !!}
        {!! Form::label($id . '1', $options[0] ?? '启用', ['class' => 'switch-lbl']) !!}
{{--        <input id="{!! $id !!}2" @if (isset($value) && !$value) checked @endif--}}
{{--               type="radio" name="{!! $id !!}" class="minimal" value="0">--}}
        {!! Form::radio($id, 0, isset($value) && !$value, [
            'id' => $id . '2', 'class' => 'minimal'
        ]) !!}
        {!! Form::label($id . '2', $options[1] ?? '禁用', ['class' => 'switch-lbl']) !!}
    </div>
</div>
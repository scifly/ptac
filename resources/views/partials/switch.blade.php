<?php $lblStyle = "margin: 0 5px; vertical-align: middle; font-weight: normal;"; ?>
<div class="form-group">
    <label for="{!! $id !!}" class="col-sm-3 control-label">
        {!! $label ?? '状态' !!}
    </label>
    <div class="col-sm-6" style="padding-top: 5px;">
        <input id="{!! $id !!}1"
               @if(!isset($value) || (isset($value) and $value)) checked @endif
               type="radio" name="{!! $id !!}" class="minimal" value="1">
        <label for="{!! $id !!}1" style="{!! $lblStyle !!}">
            {!! $options[0] ?? '启用' !!}
        </label>
        <input id="{!! $id !!}2" @if (isset($value) && !$value) checked @endif
               type="radio" name="{!! $id !!}" class="minimal" value="0">
        <label for="{!! $id !!}2" style="{!! $lblStyle !!}">
            {!! $options[1] ?? '禁用' !!}
        </label>
    </div>
</div>
<div class="form-group">
    <label for="{{ $id }}" class="col-sm-3 control-label">{{ $label }}</label>
    <div class="col-sm-6" style="padding-top: 5px;">
        <label id="{{ $id }}" style="margin-right: 5px;">
            <input id="{{ $id }}" @if($value) checked @endif
                   type="radio" name="{{ $id }}" class="minimal" value="1">
        </label> 启用
        <label id="{{ $id }}" style="margin: 0 5px 0 10px;">
            <input id="{{ $id }}" @if(!$value) checked @endif
                   type="radio" name="{{ $id }}" class="minimal" value="0">
        </label> 禁用
    </div>
</div>
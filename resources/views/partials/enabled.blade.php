<div class="form-group">
    <label for="{{ $for }}" class="col-sm-3 control-label">
        {{ $label }}
    </label>
    <div class="col-sm-6" style="margin-top: 5px;">
        <input id="{{ $for }}" type="checkbox" name="{{ $for }}" data-render="switchery"
               data-theme="default" data-switchery="true"
               @if(!empty($value)) checked @endif
               data-classname="switchery switchery-small"/>
    </div>
</div>
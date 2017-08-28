<div class="form-group">
    <label for="enabled" class="col-sm-3 control-label">
        是否启用
    </label>
    <div class="col-sm-6" style="margin-top: 5px;">
        <input id="enabled" type="checkbox" name="enabled" data-render="switchery"
               data-theme="default" data-switchery="true"
               @if(!empty($enabled)) checked @endif
               data-classname="switchery switchery-small"/>
    </div>
</div>
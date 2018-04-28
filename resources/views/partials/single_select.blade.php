<div class="form-group"
     @if (isset($divId)) id="{{ $divId }}" @endif
     @if (isset($style)) style="{{ $style }}" @endif
>
    {!! Form::label($id, $label, [
        'class' => 'col-sm-3 control-label',
    ]) !!}
    <div class="col-sm-6">
        <div class="input-group">
            <div class="input-group-addon">
                <i class="{{ $icon ?? 'fa fa-list' }}" style="width: 20px;"></i>
            </div>
            {!! Form::select($id, $items, null, [
                'class' => 'form-control select2',
                'style' => 'width: 100%;',
                'disabled' => sizeof($items) <= 1
            ]) !!}
        </div>
    </div>
</div>
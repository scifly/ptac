<div class="form-group"
     @if(isset($divId)) id="{{ $divId }}" @endif
     @if(isset($style)) style="{{ $style }}" @endif
>
    {!! Form::label($id, $label, [
        'class' => 'col-sm-3 control-label',
    ]) !!}
    <div class="col-sm-6">
        <div class="input-group">
            <div class="input-group-addon">
                <i class="@if(isset($icon)) {{ $icon }} @else fa fa-list @endif" style="width: 20px;"></i>
            </div>
            {!! Form::select($id, $items, null, [
                'class' => 'form-control select2',
                'style' => 'width: 100%;'
            ]) !!}
        </div>
    </div>
</div>
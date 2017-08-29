<div class="form-group">
    {!! Form::label($id, $label, ['class' => 'col-sm-4 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::select($id, $items, null, ['class' => 'form-control']) !!}
    </div>
</div>
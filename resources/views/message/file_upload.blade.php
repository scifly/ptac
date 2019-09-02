<div class="upload-button">
    <label for="{!! $id !!}" class="custom-file-upload text-blue">
        <i class="fa fa-cloud-upload"></i> {!! $label !!}
    </label>
    {!! Form::file($id, [
        'id' => $id,
        'accept' => $accept ?? '*',
        'class' => 'file-upload',
        $required ?? ''
    ]) !!}
    <a href="#" class="remove-file" style="display: none;">
        <i class="fa fa-close text-red"> 删除</i>
    </a><br />
    {!! Form::hidden(null, null, ['class' => 'media_id']) !!}
    @if (isset($note))
        <p class="help-block">{!! $note !!}</p>
    @endif
</div>
<div class="upload-button">
    <label for="{{ $id }}" class="custom-file-upload text-blue">
        <i class="fa fa-cloud-upload"></i> {{ $label }}
    </label>
    {!! Form::file($id, [
        'id' => $id,
        'accept' => $accept,
        'class' => 'file-upload',
        $required ?? ''
    ]) !!}
    @if (isset($note))
        <p class="help-block">{{ $note }}</p>
    @endif
</div>
{!! Form::open([
    'method' => 'post', 
    'id' => 'formAttachmentType',
    'data-parsley-validate' => 'true'
]) !!}
@include('attachment_type.create_edit')
{!! Form::close() !!}
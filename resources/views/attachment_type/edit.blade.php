{!! Form::model($at, [
    'method' => 'put',
    'id' => 'formAttachmentType',
    'data-parsley-validate' => 'true'
]) !!}
@include('attachment_type.create_edit')
{!! Form::close() !!}
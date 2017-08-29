{!! Form::open([
    'method' => 'post',
    'id' => 'formMajor',
    'data-parsley-validate' => 'true' 
]) !!}
@include('major.create_edit')
{!! Form::close() !!}
{!! Form::open([
    'method' => 'post',
    'id' => 'formBed',
    'data-parsley-validate' => 'true'
]) !!}
@include('bed.create_edit')
{!! Form::close() !!}

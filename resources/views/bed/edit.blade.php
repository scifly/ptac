{!! Form::model($bed, [
    'method' => 'put',
    'id' => 'formBed',
    'data-parsley-validate' => 'true'
]) !!}
@include('bed.create_edit')
{!! Form::close() !!}
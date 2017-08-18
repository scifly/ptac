{!! Form::open([
    'method' => 'post',
    'id' => 'formSemester',
    'data-parsley-validate' => 'true'
]) !!}
@include('semester.create_edit')
{!! Form::close() !!}
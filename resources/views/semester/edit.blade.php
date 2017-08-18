{!! Form::model($school, [
    'method' => 'put',
    'id' => 'formSemester',
    'data-parsley-validate' => 'true'
]) !!}
@include('semester.create_edit')
{!! Form::close() !!}
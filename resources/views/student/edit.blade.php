{!! Form::model($student, [
    'method' => 'put',
    'id' => 'formStudent',
    'data-parsley-validate' => 'true'
]) !!}
@include('student.create_edit')
{!! Form::close() !!}
@include('student.department_tree')
{!! Form::model($school, [
    'method' => 'put',
    'id' => 'formSchoolType',
    'data-parsley-validate' => 'true'
]) !!}
@include('school_type.create_edit')
{!! Form::close() !!}
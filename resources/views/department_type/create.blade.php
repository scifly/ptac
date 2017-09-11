{!! Form::open([
    'method' => 'post',
    'id' => 'formDepartmentType',
    'data-parsley-validate' => 'true'
]) !!}
@include('department_type.create_edit')
{!! Form::close() !!}
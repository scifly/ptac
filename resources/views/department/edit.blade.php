{!! Form::model($department, [
    'method' => 'put',
    'id' => 'formDepartment',
    'class' => 'form-horizontal form-borderd',
    'data-parsley-validate' => 'true'
]) !!}
@include('department.create_edit')
{!! Form::close() !!}

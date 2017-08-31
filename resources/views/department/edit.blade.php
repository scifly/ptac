{!! Form::model($department, [
    'method' => 'put',
    'id' => 'formDepartment',
    'class' => 'form-horizontal form-borderd',
    'data-parsley-validate' => 'true'
]) !!}
@include('custodian_student.create_edit')
{!! Form::close() !!}

{!! Form::model($op, [
    'method' => 'put',
    'id' => 'formCompany',
    'data-parsley-validate' => 'true'
]) !!}
@include('company.create_edit')
{!! Form::close() !!}
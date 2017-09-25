{!! Form::model($operator, ['url' => '/operators/' . $operator->id, 'method' => 'put', 'id' => 'formOperator', 'data-parsley-validate' => 'true']) !!}
@include('operator.create_edit')
{!! Form::close() !!}
@include('educator.department_tree')

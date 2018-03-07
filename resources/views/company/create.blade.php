{!! Form::open([
    'method' => 'post',
    'id' => 'formCompany',
    'data-parsley-validate' => 'true'
]) !!}
@include('company.create_edit')
{!! Form::close() !!}
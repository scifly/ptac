{!! Form::model($prize, [
    'method' => 'put',
    'id' => 'formPrize',
    'data-parsley-validate' => 'true'
]) !!}
@include('prize.create_edit')
{!! Form::close() !!}
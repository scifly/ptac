{!! Form::open([
        'method' => 'post',
        'id' => 'formPrize',
        'data-parsley-validate' => 'true'
    ]) !!}
@include('prize.create_edit')
{!! Form::close() !!}
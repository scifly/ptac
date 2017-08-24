    {!! Form::open([
        'method' => 'post',
        'id' => 'formProcedure',
        'data-parsley-validate' => 'true'
    ]) !!}
    @include('procedure.create_edit')
    {!! Form::close() !!}
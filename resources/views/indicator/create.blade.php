{!! Form::open([
        'method' => 'post',
        'id' => 'formIndicator',
        'data-parsley-validate' => 'true'
    ]) !!}
@include('indicator.create_edit')
{!! Form::close() !!}
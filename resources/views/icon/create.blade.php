{!! Form::open([
        'method' => 'post',
        'id' => 'formIcon',
        'data-parsley-validate' => 'true'
    ]) !!}
@include('icon.create_edit')
{!! Form::close() !!}
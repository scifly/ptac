{!! Form::open([
    'method' => 'post',
    'id' => 'formEducator',
    'data-parsley-validate' => 'true'
]) !!}
@include('educator.create_edit')
{!! Form::close() !!}
@include('partials.tree')
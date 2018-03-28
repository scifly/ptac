{!! Form::open([
    'method' => 'post', 
    'id' => 'formActionType',
    'data-parsley-validate' => 'true'
]) !!}
@include('action_type.create_edit')
{!! Form::close() !!}
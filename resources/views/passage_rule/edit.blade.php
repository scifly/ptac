{!! Form::model($pr, [
    'method' => 'put',
    'id' => 'formPassageRule',
    'data-parsley-validate' => 'true'
]) !!}
@include('passage_rule.create_edit')
{!! Form::close() !!}
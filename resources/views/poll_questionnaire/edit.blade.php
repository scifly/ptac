{!! Form::model($pq, [
    'method' => 'put',
    'id' => 'formPq',
    'data-parsley-validate' => 'true'
]) !!}
@include('poll_questionnaire.create_edit')
{!! Form::close() !!}

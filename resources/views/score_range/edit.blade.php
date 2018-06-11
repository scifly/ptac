{!! Form::model($sr, [
    'method' => 'put',
    'id' => 'formScoreRange',
    'data-parsley-validate' => 'true'
]) !!}
@include('score_range.create_edit')
{!! Form::close() !!}
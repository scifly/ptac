{!! Form::open(['method' => 'post','id' => 'formScoreRange','data-parsley-validate' => 'true']) !!}
@include('score_range.create_edit')
{!! Form::close() !!}
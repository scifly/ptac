{!! Form::model($comboType, [
    'method' => 'put',
    'id' => 'formComboType',
    'data-parsley-validate' => 'true'
]) !!}
@include('combo_type.create_edit')
{!! Form::close() !!}
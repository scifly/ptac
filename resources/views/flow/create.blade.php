<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('shared.form_header')
    </div>
    <div class="box-body">
        {!! Form::open([
            'method' => 'post',
            'id' => 'formProcedureLogCreate',
            'data-parsley-validate' => 'true'
        ]) !!}
        <div class="form-horizontal">
            @include('shared.single_select', [
                'label' => '审批流程',
                'id' => 'flow_type_id',
                'items' => $flowTypes
            ])
            <div class="form-group">
                @include('shared.label', ['field' => 'remark', 'label' => '留言'])
                <div class="col-sm-6">
                    {!! Form::text('留言', null, [
                        'class' => 'form-control text-blue',
                        'placeholder' => '（选填）',
                    ]) !!}
                </div>
            </div>
            @include('flow.attachment')
            @include('shared.form_buttons')
        </div>
        {!! Form::close() !!}
    </div>
</div>

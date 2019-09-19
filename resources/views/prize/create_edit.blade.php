<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('shared.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (isset($prize))
                {!! Form::hidden('id', $prize['id'], ['id' => 'id']) !!}
            @endif
            <div class="form-group">
                @include('shared.label', ['field' => 'name', 'label' => '名称'])
                <div class="col-sm-6">
                    {!! Form::text('name', null, [
                        'class' => 'form-control text-blue',
                        'required' => 'true',
                        'maxlength' => '80'
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                @include('shared.label', ['field' => 'score', 'label' => '分数'])
                <div class="col-sm-6">
                    {!! Form::number('score', null, [
                        'class' => 'form-control text-blue',
                        'required' => 'true',
                    ]) !!}
                </div>
            </div>
            @include('shared.remark')
            @include('shared.switch', [
                'id' => 'enabled',
                'value' => $prize['enabled'] ?? null
            ])
        </div>
    </div>
    @include('shared.form_buttons')
</div>
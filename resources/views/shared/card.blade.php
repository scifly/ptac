<!-- 一卡通卡号 -->
<div class="form-group">
    {!! Form::label('card[sn]', '一卡通卡号', [
        'class' => 'col-sm-3 control-label'
    ]) !!}
    <div class="col-sm-6">
        <div class="input-group">
            @include('shared.icon_addon', ['class' => 'fa-credit-card'])
            {!! Form::text('card[sn]', null, [
                'class' => 'form-control text-blue',
                'placeholder' => '(可选)',
                'data-parsley-type' => 'alphanum',
                'data-parsley-length' => '[2, 32]'
            ]) !!}
        </div>
    </div>
</div>
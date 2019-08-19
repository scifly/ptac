{!! Form::open([
    'method' => 'post',
    'id' => 'formTemplate',
    'data-parsley-validate' => 'true'
]) !!}
<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('shared.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @include('shared.single_select', [
                 'label' => '公众号',
                 'id' => 'app_id',
                 'items' => $apps,
                 'icon' => 'fa fa-weixin text-green'
            ])
            @include('shared.single_select', [
                 'label' => '主营行业',
                 'id' => 'primary',
                 'items' => $industries,
                 'icon' => 'fa fa-industry'
            ])
            @include('shared.single_select', [
                 'label' => '副营行业',
                 'id' => 'secondary',
                 'items' => $industries,
                 'icon' => 'fa fa-industry'
            ])
        </div>
    </div>
    @include('shared.form_buttons', ['id' => 'save'])
</div>
{!! Form::close() !!}
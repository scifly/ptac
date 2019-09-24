<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('shared.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (isset($article) && !empty($article['id']))
                {{ Form::hidden('id', $article['id']) }}
            @endif
            @include('shared.single_select', [
                'label' => '所属网站模块',
                'id' => 'wsm_id',
                'items' => $wsms
            ])
            <div class="form-group">
                {!! Form::label('name', '名称', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('name', null, [
                        'class' => 'form-control text-blue',
                        'placeholder' => '不能超过40个汉字',
                        'required' => 'true',
                        'data-parsley-length' => '[2, 40]'
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('summary', '文章摘要', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('summary', null, [
                        'class' => 'form-control text-blue',
                        'placeholder' => '不能超过60个汉字',
                        'required' => 'true',
                        'data-parsley-length' => '[2, 60]'
                    ]) !!}
                </div>
            </div>
            @include('shared.wapsite.preview')
            <div class="form-group">
                {!! Form::label('content', '文章内容', [
                    'class' => 'control-label col-sm-3'
                ]) !!}
                <div class="col-sm-6">
                    <div class="preview_content">
                        <script id="container" name="content" type="text/plain" >
                            @if (isset($article))
                                {!! $article['content'] !!}
                            @endif
                        </script>
                    </div>
                </div>
            </div>
            @include('shared.switch', [
                'id' => 'enabled',
                'value' => $article['enabled'] ?? null
            ])
        </div>
    </div>
    @include('shared.form_buttons')
</div>
@include('shared.wapsite.modal_uploader')
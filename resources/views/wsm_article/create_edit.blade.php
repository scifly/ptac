<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (isset($article) && !empty($article['id']))
                {{ Form::hidden('id', $article['id'], ['id' => 'id']) }}
            @endif
            @include('partials.single_select', [
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
            <div class="form-group">
                {!! Form::label('media_ids', '轮播图', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="preview">
                        @if(isset($medias))
                            @foreach ($medias as $key => $value)
                                @if (!empty($value))
                                    <div class="img-item">
                                        <img src="../../{{ $value->path }}" id="{{ $value->id }}">
                                        <input type="hidden" name="media_ids[]" value="{{ $value->id }}"/>
                                        <div class="del-mask">
                                            <i class="delete fa fa-trash"></i>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @endif
                    </div>
                    <a id="upload" href="#" data-toggle="modal" data-target="#modalPic">
                        <i class="fa fa-cloud-upload"></i> 上传图片
                    </a>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('content', '文章内容', ['class' => 'control-label col-sm-3']) !!}
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
                @include('partials.enabled', [
                    'id' => 'enabled',
                    'value' => $article['enabled'] ?? null
                ])
        </div>
    </div>
    @include('partials.form_buttons')
</div>
<div class="modal fade" id="modalPic">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"
                        aria-hidden="true">×
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    上传文件
                </h4>
            </div>
            <div class="modal-body">
                {!! Form::file('images[]', [
                    'id' => 'uploadFiles',
                    'accept' => 'image/*',
                    'multiple'
                ]) !!}
            </div>
            <div class="modal-footer">
                {!! Form::button('关闭', [
                    'class' => 'btn btn-default',
                    'data-dismiss' => 'modal'
                ]) !!}
                {{--<button type="button" class="btn btn-default"--}}
                        {{--data-dismiss="modal">关闭--}}
                {{--</button>--}}
            </div>
        </div>
    </div>
</div>
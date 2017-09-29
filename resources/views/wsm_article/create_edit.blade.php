<div class="box box-widget">
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
                <div class="col-sm-3">
                    {!! Form::text('name', null, [
                        'class' => 'form-control',
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
                <div class="col-sm-3">
                    {!! Form::text('summary', null, [
                        'class' => 'form-control',
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
                            @foreach($medias as $key => $value)
                                @if(!empty($value))
                                    <div class="img-item">
                                        <img src="../../{{$value->path}}" id="{{$value->id}}">
                                        <input type="hidden" name="media_ids[]" value="{{$value->id}}"/>
                                        <div class="del-mask">
                                            <i class="delete fa fa-trash"></i>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @endif
                    </div>
                    <a class="btn btn-primary" data-toggle="modal" data-target="#modalPic">上传</a>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('content', '文章内容', ['class' => 'control-label col-sm-3']) !!}
                <div class="col-sm-6">
                    <div class="preview">
                        <script id="container" name="content" type="text/plain">
                            @if(isset($article))
                                {!! $article['content'] !!}
                            @endif
                        </script>
                    </div>
                </div>
            </div>
                @include('partials.enabled', [
                    'label' => '是否启用',
                    'id' => 'enabled',
                    'value' => isset($article['enabled']) ? $article['enabled'] : NULL
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
                    模态框（Modal）标题
                </h4>
            </div>
            <div class="modal-body">
                <input type="file" name="img[]" id="uploadFile" accept="image/jpeg,image/gif,image/png" multiple>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default"
                        data-dismiss="modal">关闭
                </button>
            </div>
        </div>
    </div>
</div>
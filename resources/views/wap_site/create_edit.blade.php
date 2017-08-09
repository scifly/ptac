<style>
    .preview img{
        width: 100%;
        height: 100px;
        margin: 10px;
    }
</style>
<div class="box box-primary">
    <div class="box-header"></div>
    <div class="box-body">
        <div class="form-horizontal" >
            <div class="form-group">
                {!! Form::label('site_title', '首页抬头',['class' => 'col-sm-2 control-label']) !!}
                <div class="col-sm-3">
                    {!! Form::text('site_title', null, [
                    'class' => 'form-control',
                    'placeholder' => '不能超过40个汉字',
                    'data-parsley-required' => 'true',
                    'data-parsley-maxlength' => '40',
                    'data-parsley-minlength' => '2',
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('school_id', '所属学校',['class' => 'col-sm-2 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::select('school_id', $schools, null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('media_ids', '轮播图',['class' => 'col-sm-2 control-label']) !!}
                <div class="col-sm-6">
                    @if(isset($medias))
                        @foreach($medias as $key => $value)
                            <img src="../../..{{$value->path}}">
                            <input type="hidden" name="media_ids[]" value="{{$value->id}}"/>
                        @endforeach
                    @endif
                    <div class="preview" style="width: 100px;overflow: hidden;"></div>
                    <a class="btn btn-primary" data-toggle="modal" data-target="#modalPic">上传</a>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-3 col-sm-offset-2">
                    {!! Form::radio('enabled', '1', true) !!}
                    {!! Form::label('enabled', '启用') !!}
                    {!! Form::radio('enabled', '0') !!}
                    {!! Form::label('enabled', '禁用') !!}
                </div>
            </div>
        </div>
    </div>
    <div class="box-footer">
        {{--button--}}
        <div class="form-group">
            <div class="col-sm-6 col-sm-offset-2">
                {!! Form::submit('保存', ['class' => 'btn btn-primary pull-left', 'id' => 'save']) !!}
                {!! Form::reset('取消', ['class' => 'btn btn-default pull-right', 'id' => 'cancel']) !!}
            </div>
        </div>
    </div>
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
                <form action="#" class="form-horizontal" >
                    <input type="file" id="uploadFile" accept="image/jpeg,image/gif,image/png" multiple>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default"
                        data-dismiss="modal">关闭
                </button>
                <button type="button" class="btn btn-primary" id="upload">
                    上传
                </button>
            </div>
        </div>
    </div>
</div>

<div class="box box-default box-solid">
    {{ Form::hidden('id', $ws['id']) }}
    <div class="box-header with-border">
        @include('shared.form_header', ['disabled' => true])
    </div>
    <div class="box box-primary" style="margin-top:10px;">
        <div class="box-body box-profile" style="position: relative;padding: 20px;text-align: center;">
            <img class="avater" src="{!! asset("img/window-icon.png") !!}" alt="">
            <div class="maininfo">
                <h3 class="profile-username">{{ $ws->site_title }}</h3>
            </div>
            <a href="#" class="btn btn-primary btn-block btn-bianji" style="">
                <i class="fa fa-edit"> 编辑</i>
            </a>
        </div>
    </div>
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">其他信息</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body" style="margin-left: 10px;">
            <div class="col-lg-6 otherinfo-con">
                <strong class="title">所属学校</strong>
                <p class="text-muted">
                    {{ $ws->school->name }}
                </p>
                <hr>
            </div>
            <div class="col-lg-6 otherinfo-con">
                <strong class="title">状态</strong>
                <p class="text-muted">{{ $ws->enabled ? '已启用' : '未启用' }}</p>
                <hr>
            </div>
        </div>
    </div>
</div>
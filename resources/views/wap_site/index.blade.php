{{--<div class="box box-default box-solid">--}}
    {{--<div class="box-header with-border">--}}
        {{--@include('partials.list_header')--}}
    {{--</div>--}}
    {{--<div class="box-body">--}}
        {{--<table id="data-table" style="width: 100%"--}}
               {{--class="display nowrap table table-striped table-bordered table-hover table-condensed">--}}
            {{--<thead>--}}
			{{--<tr class="bg-info">--}}
                {{--<th>#</th>--}}
                {{--<th>所属学校</th>--}}
                {{--<th>首页抬头</th>--}}
                {{--<th>创建于</th>--}}
                {{--<th>更新于</th>--}}
                {{--<th>状态</th>--}}
            {{--</tr>--}}
            {{--</thead>--}}
            {{--<tbody></tbody>--}}
        {{--</table>--}}
    {{--</div>--}}
    {{--@include('partials.form_overlay')--}}
{{--</div>--}}
<style>
    .avater {
        width: 150px;
        border-radius: 50%;
        margin-left: 30px;
        float: left;
    }

    .maininfo {
        float: left;
        margin-left: 50px;
        text-align: left;
    }

    .text-muted {
        font-size: 17px;
        margin: 10px 0 0 0;
    }

    .title {
        font-size: 18px;
        letter-spacing: 7px;
    }

    .profile-username {
        margin-top: 20px;
    }

    .btn-bianji {
        width: 80px;
        position: absolute;
        right: 20px;
        bottom: 20px;
    }

    .otherinfo-con {
        border-right: 1px solid #eee;
    }

    .maininfo {
        margin-top: 20px;
    }

    @media (max-width: 900px) {

        .avater {
            margin-left: 0;
            float: none;
        }

        .maininfo {
            float: none;
            margin-left: 0;
            text-align: center;
        }

        .profile-username {
            margin-top: 15px;
        }

        .btn-bianji {
            width: 100%;
            position: relative;
            right: 0;
            bottom: 0;
            margin-top: 20px;
        }

        .otherinfo-con {
            border-right: 0;
        }
    }
</style>
<div class="box box-default box-solid">
    @if (!empty($wapSite['id']))
        {{ Form::hidden('id', $wapSite['id'], ['id' => 'id']) }}
    @endif
    @if(isset($breadcrumb))
        <div class="box-header with-border">
            @include('partials.form_header')
        </div>
    @endif
    <div class="box box-primary" style="margin-top:10px;">
        <div class="box-body box-profile" style="position: relative;padding: 20px;text-align: center;">
            <img class="avater" src='{{asset("../public/img/avatar5.png")}}' alt="User profile picture">

            <div class="maininfo">
                <h3 class="profile-username">{{$wapSite->site_title}}</h3>

            </div>

            <a href="#" class="btn btn-primary btn-block btn-bianji" style=""><b>编辑</b></a>
        </div>
        <!-- /.box-body -->
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
                    {{ $wapSite->school->name }}
                </p>
                <hr>
            </div>

            <div class="col-lg-6 otherinfo-con">
                <strong class="title">状态</strong>

                <p class="text-muted">{{$wapSite->enabled == 1 ? '已启用' : '未启用'}}</p>

                <hr>
            </div>
        </div>
    </div>
</div>



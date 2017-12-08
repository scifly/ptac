<style>
    .avater{width: 150px;border-radius:50%;margin-left: 30px;float: left;}
    .maininfo{float: left;margin-left: 50px;text-align: left;}
    .text-muted{font-size: 17px;margin:10px 0 0 0 ;}
    .title{font-size: 18px;letter-spacing:7px;}
    .profile-username{margin-top: 20px;}
    .btn-bianji{width: 80px;position: absolute;right: 20px;bottom: 20px;}
    .otherinfo-con{border-right:1px solid #eee;}
    @media (max-width: 900px){

        .avater{margin-left: 0;float: none;}
        .maininfo{float: none;margin-left:0;text-align: center;}
        .profile-username{margin-top:15px;}
        .btn-bianji{width:100%;position: relative;right: 0;bottom: 0;margin-top: 20px;}
        .otherinfo-con{border-right:0;}
    }
</style>

<!--<div class="col-lg-8">
          
</div>-->
<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box box-primary" style="margin-top:10px; border-top:4px solid #00c0ef  !important">
        <div class="box-body box-profile" style="position: relative;padding: 20px;text-align: center;">
            <img class="avater" src="../../dist/img/user4-128x128.jpg" alt="User profile picture">

            <div class="maininfo">
                <h3 class="profile-username">姓名 : {{$student->user->realname}}</h3>

                <h3 class="profile-username">性别 : {{$student->user->gender}}</h3>

                <h3 class="profile-username">英文名 : {{$student->user->english_name}}</h3>
            </div>

            <a href="#" class="btn btn-primary btn-block btn-bianji" style=""><b>编辑</b></a>
        </div>
        <!-- /.box-body -->
    </div>

    <div class="box box-primary" style="border-top:4px solid #00c0ef  !important">
        <div class="box-header with-border">
            <h3 class="box-title">其他信息</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body" style="margin-left: 10px;">
            <div class="col-lg-6 otherinfo-con">
                <strong class="title"><i class="fa fa-mobile"></i> 手机</strong>

                <p class="text-muted">
                    123123123123123
                </p>
                <p class="text-muted">
                    123123123123123
                </p>
                <hr>

                <strong class="title"><i class="fa fa-phone"></i> 座机</strong>

                <p class="text-muted">12312312312312</p>

                <hr>

                <strong class="title"><i class="fa fa-envelope-o"></i> 邮箱</strong>

                <p class="text-muted">{{$student->user->email}}</p>

                <hr>

                <strong class="title"><i class=" fa fa-object-group"></i> 所属年级</strong>

                <p class="text-muted">2017</p>

                <hr>

                <strong class="title"><i class="fa fa-users"></i> 所属班级</strong>

                <p class="text-muted">12312312312312</p>

                <hr>
            </div>
            <div class="col-lg-6 otherinfo-con">
                <strong class="title">学号</strong>

                <p class="text-muted">{{$student->student_number}}</p>

                <hr>

                <strong class="title">卡号</strong>

                <p class="text-muted">{{$student->card_number}}</p>

                <hr>

                <strong class="title">生日</strong>

                <p class="text-muted">{{$student->birthday}}</p>

                <hr>

                <strong class="title">是否住校</strong>

                <p class="text-muted">12312312312312</p>

                <hr>

                <strong class="title">备注</strong>

                <p class="text-muted">{{$student->remark}}</p>

                <hr>

                <strong class="title">状态</strong>

                <p class="text-muted">{{$student->enabled ? '已启用' : '未启用'}}</p>

                <hr>
            </div>


        </div>
        <!-- /.box-body -->
    </div>
</div>
        
     
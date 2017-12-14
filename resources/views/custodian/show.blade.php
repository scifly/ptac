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
    @if (!empty($custodian['id']))
        {{ Form::hidden('id', $custodian['id'], ['id' => 'id']) }}
    @endif
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box box-primary" style="margin-top:10px; border-top:4px solid #00a65a !important">
        <div class="box-body box-profile" style="position: relative;padding: 20px;text-align: center;">
            <img class="avater" src='{{asset("../public/img/avatar5.png")}}' alt="User profile picture">
            <div class="maininfo">
                <h3 class="profile-username">姓名 : {{ $custodian->user->realname }}</h3>
                <h3 class="profile-username">性别 : {{ $custodian->user->gender }}</h3>
                <h3 class="profile-username">英文名 : {{ $custodian->user->english_name }}</h3>
            </div>
            <a href="#" class="btn btn-primary btn-block btn-bianji" style=""><b>编辑</b></a>
        </div>
        <!-- /.box-body -->
    </div>
    <div class="box box-primary" style="border-top:4px solid #00a65a !important">
        <div class="box-header with-border">
            <h3 class="box-title">其他信息</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body" style="margin-left: 10px;">
            <div class="col-lg-6 otherinfo-con">
                <strong class="title"><i class="fa fa-mobile"></i> 手机</strong>
                <p class="text-muted">
                    @foreach($custodian->user->mobiles as $mobile){{ $mobile['mobile'] }}@endforeach
                </p>
                <hr>
                <strong class="title"><i class="fa fa-phone"></i> 座机</strong>
                <p class="text-muted">{{ $custodian->user->telephone }}</p>
                <hr>
                <strong class="title"><i class="fa fa-envelope-o"></i> 邮箱</strong>
                <p class="text-muted">{{ $custodian->user->email }}</p>
                <hr>
                <strong class="title">状态</strong>
                <p class="text-muted">{{ $custodian->enabled == 1 ? '已启用' : '未启用' }}</p>
                <hr>
            </div>
            <div class="col-lg-6 otherinfo-con">
                <strong class="title"><i class=" fa fa-object-group"></i> 被监护人</strong>
                <table class="table table-striped table-bordered table-hover table-condensed" style="margin-top: 10px;">
                    <thead>
                    <tr class="bg-info">
                        <th>学生</th>
                        <th>学号</th>
                        <th>监护人关系</th>
                        <th>状态</th>
                    </tr>
                    </thead>
                    <tbody id="tBody">
                    <tr>
                        <input type="hidden" value="11" name="student_ids[0]" id="student_ids">
                        @foreach($custodian->students as $student)
                        <td>{{ $student->user->realname }}</td>
                        <td>{{ $student->student_number }}</td>
                        <td>{{ $custodian->custodianStudents()
                                ->where('custodian_id', $custodian->id)
                                ->where('student_id', $student->id)
                                ->first()
                                ->relationship
                            }}
                        </td>
                        <td>
                            {{ $custodian->custodianStudents()
                                ->where('custodian_id', $custodian->id)
                                ->where('student_id', $student->id)
                                ->first()
                                ->enabled == 1 ? '已启用' : '未启用'
                            }}
                        </td>
                        @endforeach
                    </tr>
                    </tbody>
                </table>
                <hr>
            </div>

        </div>
        <!-- /.box-body -->
    </div>
</div>
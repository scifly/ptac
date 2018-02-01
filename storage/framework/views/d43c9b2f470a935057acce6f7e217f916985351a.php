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
<section class="content clearfix">
    <?php echo $__env->make('partials.modal_dialog', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    <div class="col-lg-12">
        <div class="nav-tabs-custom">
            <div class="box box-default box-solid">
                <?php if(isset($breadcrumb)): ?>
                    <div class="box-header with-border">
                        <span id="breadcrumb" style="color: #999; font-size: 13px;"><?php echo $breadcrumb; ?></span>
                    </div>
                <?php endif; ?>
                <form id="school-form">
                    <?php if(!empty($school['id'])): ?>
                        <?php echo e(Form::hidden('id', $school['id'], ['id' => 'id'])); ?>

                    <?php endif; ?>
                        <?php echo e(csrf_field()); ?>

                    <div class="box box-primary" style="margin-top:10px;">
                        <div class="box-body box-profile" style="position: relative;padding: 20px;text-align: center;">
                            <img class="avater" src='<?php echo e(asset("../img/school-1.png")); ?>' alt="User profile picture">
                            <div class="maininfo">
                                <input class="profile-username edit-school" id="name" name="name"
                                       value="<?php echo e($school->name); ?>" style="border: 0;background-color: #fff"
                                       readonly>
                                <a class="edit_input" style="   top: 0;right: -25px;line-height:34px" title="编辑"
                                   href="#">
                                    <i class="fa fa-pencil"></i>
                                </a>
                            </div>
                            
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
                                <strong class="title" style="display: block">地址</strong>
                                <input class="text-muted edit-school" id="address" name="address"
                                       value="<?php echo e($school->address); ?>" style="border: 0;background-color: #fff"
                                       readonly>
                                <a class="edit_input" title="编辑" href="#">
                                    <i class="fa fa-pencil"></i>
                                </a>
                                <hr>
                                <strong class="title">学校类型</strong>
                                <p class="text-muted"><?php echo e($school->schoolType->name); ?></p>
                                <?php echo e(Form::hidden('school_type_id', $school->school_type_id, ['id' => 'school_type_id'])); ?>

                                <hr>
                            </div>
                            <div class="col-lg-6 otherinfo-con">
                                <strong class="title">所属企业</strong>
                                <p class="text-muted"><?php echo e($school->corp->name); ?></p>
                                <?php echo e(Form::hidden('corp_id', $school->corp_id, ['id' => 'corp_id'])); ?>

                                <hr>
                                <strong class="title">状态</strong>
                                <p class="text-muted"><?php echo e($school->enabled == 1 ? '已启用' : '未启用'); ?></p>
                                <?php echo e(Form::hidden('enabled', $school->enabled, ['id' => 'enabled'])); ?>

                                <hr>
                            </div>
                            <?php echo e(Form::hidden('department_id', $school->department_id, ['id' => 'department_id'])); ?>

                            <?php echo e(Form::hidden('menu_id', $school->menu_id, ['id' => 'menu_id'])); ?>

                            <?php echo e(Form::hidden('signature', $school->signature, ['id' => 'signature'])); ?>

                        </div>
                    </div>
                </form>
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">地图</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php if(isset($js)): ?>
    <script src="<?php echo e(asset($js)); ?>"></script>
<?php endif; ?>

       
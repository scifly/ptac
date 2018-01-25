<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
	<meta name="csrf_token" content="<?php echo e(csrf_token()); ?>" id="csrf_token">
    <?php echo $__env->yieldContent('title'); ?>
    
    <link rel="stylesheet" href="<?php echo e(asset('/css/weui.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('/css/jquery-weui.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('/css/wechat/icon/iconfont.css')); ?>">
    <?php echo $__env->yieldContent('css'); ?>
</head>
<body ontouchstart>
<div style="height: 100%;" id="app">
    <?php echo $__env->yieldContent('content'); ?>
</div>
<?php echo $__env->yieldContent('search'); ?>

<script src="<?php echo e(asset('/js/jquery.min.js')); ?>"></script>
<script src="<?php echo e(asset('/js/fastclick.js')); ?>"></script>
<script src="<?php echo e(asset('/js/jquery-weui.min.js')); ?>"></script>
<script>
    $(function() {
        FastClick.attach(document.body);
    });
</script>
<script src="<?php echo e(asset('/js/plugins/echarts/echarts.common.min.js')); ?>"></script>
<?php echo $__env->yieldContent('script'); ?>
</body>
</html>

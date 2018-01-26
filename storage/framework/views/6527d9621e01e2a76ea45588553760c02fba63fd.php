<?php echo Form::open(['url' => '/wsm_articles', 'method' => 'post','id' => 'formWsmArticle','data-parsley-validate' => 'true']); ?>

<?php echo $__env->make('wsm_article.create_edit', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php echo Form::close(); ?>


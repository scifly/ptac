<?php echo Form::model($article, ['method' => 'put', 'id' => 'formWsmArticle']); ?>

<?php echo $__env->make('wsm_article.create_edit', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php echo Form::close(); ?>
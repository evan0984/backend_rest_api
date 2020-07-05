<?php
/**
 * @var yii\web\View $this
 * @var string $content
 */


$this->beginContent('@frontend/views/layouts/_clear.php')
?>


<main class="flex-shrink-0" role="main">
    <?php echo $content ?>
</main>


<?php $this->endContent() ?>
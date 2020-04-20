<?php require 'inc/head.php';

?>
<section class="cookies container-fluid">
    <?php
    $items = $sessionManage->getManagedSession()->getItemsInCart();

    for( $i=0;$i<count($items);$i++) {
        $anArticle=$items[$i];

        ?>
    <div class="row">
        Article Name : <?= $anArticle->getName() ?> ----  Article Qte : <?= $anArticle->getQte() ?>
    </div>
    <?php } ?>
</section>
<?php require 'inc/foot.php'; ?>

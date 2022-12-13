<?php

/** @var CadUnico\Plugin $plugin */

use MapasCulturais\i;

$opportunity = $plugin->opportunity;
$today = new DateTime();

?>
<div style="display: flex;justify-content: center;">
    <div class='cadunico' style="border: solid 2px #ede9e9;margin-bottom: 33px;width: 35%;padding: 10px;border-radius: 5px;">
        <?php if ($title = $plugin->text('home.featuredTitle') ?: $opportunity->name) : ?>
            <h2><?= $title ?></h2>
        <?php endif; ?>

        <?php if ($img = $plugin->config['featured.imageUrl'] ?: $opportunity->avatar->url ?? null) : ?>
            <img src="<?= $img ?>" alt="" style="width:<?= $plugin->config['featured.imageWidth'] ?>">
        <?php endif; ?>

        <?php //if($text = $plugin->text('home.featuredText') ?: $opportunity->shortDescription): 
        ?>
        <!-- <p><?php //$text 
                ?></p> -->
        <?php //endif; 
        ?>


        <?php if ($today < $plugin->fromDate) : ?>
            <div class="cadunico-button">
                <?= $plugin->text("home.featuredSubscription") ?>
            </div>
        <?php elseif ($today > $plugin->toDate) : ?>
            <br>
            <div class="cadunico-button">
                <?= $plugin->text('home.featiredRegistrationClosed'); ?>
            </div>
            <br>
        <?php elseif ($plugin->isRegistrationOpen()) : ?>
            <br>
            <div class="cadunico-button">
                <a class="btn btn-primary btn-large bg-btn-collor-home" href="<?= $app->createUrl($plugin->slug, 'cadastro') ?>" style="background-color: #FFAA02;">
                    <?= $plugin->text('home.featuredButton') ?>
                </a>
            </div>
            <br>
        <?php endif ?>
    </div>
</div>
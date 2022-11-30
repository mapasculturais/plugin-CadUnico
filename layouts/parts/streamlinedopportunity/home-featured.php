<?php
/** @var StreamlinedOpportunity\Plugin $plugin */

use MapasCulturais\i;

$opportunity = $plugin->opportunity;
$today = new DateTime();

?>
<div class='streamlinedopportunity'>
    <?php if($title = $plugin->text('home.featuredTitle') ?: $opportunity->name): ?>
        <h2><?= $title ?></h2>
    <?php endif; ?>

    <?php if($img = $plugin->config['featured.imageUrl'] ?: $opportunity->avatar->url ?? null): ?>
        <img src="<?= $img ?>" alt="">
    <?php endif; ?>

    <?php if($text = $plugin->text('home.featuredText') ?: $opportunity->shortDescription): ?>
        <p><?= $text ?></p>
    <?php endif; ?>
    
    
    <?php if($today < $plugin->fromDate): ?>
    <div class="streamlinedopportunity-button">
        <?= $plugin->text("home.featuredSubscription") ?>
    </div>
    <?php elseif($today > $plugin->toDate): ?>
    <div class="streamlinedopportunity-button">
        <?= $plugin->text('home.featiredRegistrationClosed');?>
    </div>
    <?php elseif($plugin->isRegistrationOpen()): ?>
        <div class="streamlinedopportunity-button">
            <a class="btn btn-primary btn-large" href="<?= $app->createUrl($plugin->slug, 'cadastro') ?>">
                <?= $plugin->text('home.featuredButton') ?>
            </a>
        </div>
    <?php endif ?>
</div>
<?php

use MapasCulturais\i;
$app = \MapasCulturais\App::i();
$plugin = $this->controller->plugin;

/**
 * Carregada na tela inicial
 * Contém as informações resumidas do cadastro não enviado e link para tela de edição 
 */
?>

<div id="" class="lab-option draf-option">
    <a href="<?= $registrationUrl; ?>">
    <button class="informative-box lab-option has-status status-<?= $registration->status ?>">
        <div class="informative-box--status">
            <?php echo $registrationStatusName; ?>
        </div>
        <div class="informative-box--icon">
            <i class="fas fa-users"></i>
        </div>

        <div class="informative-box--title">
            <h2><?= $registration->opportunity->name ?></h2>
            <i class="far fa-check-circle"></i>
        </div>

        <div class="informative-box--content" data-content="">
            <span class="more"> <?= $plugin->text("status.moreInformation");?> </span>
            <div class="content">
                <div class="item">
                </div>

                <?php if(!empty($registration->sentTimestamp)): ?>
                    <div class="item">
                        <span class="label"><?= $plugin->text("status.shippingDate"); ?></span> <?php echo $registration->sentTimestamp ? $registration->sentTimestamp->format(\MapasCulturais\i::__('d/m/Y à\s H:i')) : ''; ?>. </br>
                    </div>
                <?php endif; ?>

                <div class="item">
                    <span class="label"><?= $plugin->text("status.responsible");?></span> <?php echo $registration->owner->name; ?> </br>
                </div>

                <div class="item">
                    <span class="label"><?= $plugin->text("status.labelCpf");?></span> <?php echo $registration->owner->documento; ?>
                </div>
            </div>
        </div>
    </button>
        
    </a>


</div>
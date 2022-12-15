<?php

/** 
 * @var MapasCulturais\App $app
 * @var MapasCulturais\Themes\BaseV1\Theme $this
 * 
 * Variáveis requeridas:
 * @var CadUnico\Plugin $plugin 
 * @var MapasCulturais\Entities\Registration $registration
 * @var string  $registrationStatusName;
 */

use MapasCulturais\i;
$plugin = $this->controller->plugin;

/**
 * Contém as informações resumidas do cadastro
 * Exibida na tela inicial e na tela de status (acompanhamento) 
 * 
 * 
 */

?>


<button class="informative-box lab-option has-status status-<?= $registration->status ?>">
    <div class="informative-box--status">
        <?php echo $registrationStatusName; ?> 
    </div>
    <div class="informative-box--icon">
        <i class="fas fa-users"></i>
    </div>

    <div class="informative-box--title">
        <h2> <?= $registration->opportunity->name ?> </h2>
        <i class="far fa-check-circle"></i>
    </div>

    <div class="informative-box--content" data-content="">
        <span class="more"> <?= $plugin->text("status.moreInformation");?> </span>
        <div class="content">
            <div class="item">  
            </div>

            <div class="item">
                <span class="label"><?= $plugin->text("status.shippingDate"); ?> </span> <?php echo $registration->sentTimestamp ? $registration->sentTimestamp->format(\MapasCulturais\i::__('d/m/Y à\s H:i')): ''; ?>.  </br>
            </div>

            <div class="item">
                <span class="label"><?= $plugin->text("status.responsible");?></span>  <?php echo $registration->owner->name; ?> </br>
            </div>
            
            <div class="item">
                <span class="label"><?= $plugin->text("status.labelCpf");?></span> <?php echo $registration->owner->documento; ?>
            </div>
        </div>
    </div>
</button>

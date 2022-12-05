<?php
/** 
 * @var MapasCulturais\App $app
 * @var MapasCulturais\Themes\BaseV1\Theme $this
 * 
 * Variáveis requeridas:
 * @var CadUnico\Plugin $plugin 
 * @var MapasCulturais\Entities\Opportunity $opportunity
 */

use MapasCulturais\i;
use MapasCulturais\Entities\Registration;

$slug = $plugin->slug;

$this->jsObject['opportunityId'] = $opportunity->id;
$profile = $app->user->profile;

?>
<section class="lab-main-content cadastro">
    <header>
        <div class="intro-message">
            <div class="name"> 
                <?= $plugin->text('dashboard.welcome');?> <?= $profile->name ? ", " . $profile->name : "" ?>! <br>
                <?= sprintf( $plugin->text('dashboard.buttonBack'), $app->baseUrl) ?>
            </div>
        </div>
    </header>

    <div class="js-lab-item lab-item cadastro-options">
        <?php  if (count($registrations) < $plugin->limit) { ?>
            <div class="long-description">
                <?= $plugin->text('dashboard.description') ?: $opportunity->shortDescription ?>
            </div>

            <h2 class="featured-title">
                <?php if($plugin->hasSealGovbr()):?>
                    <?= $plugin->text('dashboard.title') ?>
                <?php else:?>
                    <?= $plugin->text('dashboard.titleGovbr') ?>
                <?php endif?>
            </h2>   
        <?php } else {?>
            <h2 class="featured-title">
                <?= $plugin->text('dashboard.applicationSummaryTitle') ?>
            </h2>        
        <?php } ?>

        <div class="lab-form-filter opcoes-inciso">
          
            <?php if (count($registrations) < $plugin->limit && $plugin->isRegistrationOpen()): ?>
                    <?php if($plugin->hasSealGovbr()):?>
                        <?php $this->part("cadunico/button-registration", ['plugin' => $plugin, 'opportunity' => $opportunity])?>
                    <?php else:?>
                        <?php $this->part("cadunico/govbr-sing-in", ['plugin' => $plugin])?>
                    <?php endif?>
            <?php endif; ?>
            <?php 
            foreach ($registrations as $registration) {
                $registrationUrl = $this->controller->createUrl('formulario', [$registration->id]);
                switch ($registration->status) {
                        //caso seja nao enviada (Rascunho)
                    case Registration::STATUS_DRAFT:
                        $this->part('cadunico/cadastro/application-draft',  [
                            'registration' => $registration, 
                            'registrationUrl' => $registrationUrl, 
                            'registrationStatusName' => i::__('Cadastro iniciado', 'cad-unico')
                        ]);
                        break;
                        //caso  tenha sido enviada
                    default:
                        $registrationStatusName = $summaryStatusName[$registration->status];
                        $this->part('cadunico/cadastro/application-status',  [
                            'registration' => $registration, 
                            'registrationStatusName' => $registrationStatusName
                        ]);
                        break;
                }
            }
            ?>
        </div>

    </div><!-- End item -->
</section>
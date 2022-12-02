<?php
/** 
 * @var CadUnico\Plugin $plugin 
 * @var MapasCulturais\Entities\Opportunity $opportunity
 * @var MapasCulturais\Themes\BaseV1\Theme $this
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
                <?= $plugin->text('dashboard.title') ?>
            </h2>   
        <?php } else {?>
            <h2 class="featured-title">
                <?= $plugin->text('dashboard.applicationSummaryTitle') ?>
            </h2>        
        <?php } ?>

        <div class="lab-form-filter opcoes-inciso">
          
            <?php if (count($registrations) < $plugin->limit && $plugin->isRegistrationOpen()): ?>
                <button onclick="location.href='<?= $this->controller->createUrl('novaInscricao', ['agent' => $profile->id]) ?>'" clickable id="option3" class="informative-box lab-option">
                    <div class="informative-box--icon">
                        <i class="fas fa-user"></i>
                    </div>

                    <div class="informative-box--title">
                        <h2><?= $plugin->text('dashboard.button') ?: $opportunity->name ?></h2>
                        <i class="fas fa-minus"></i>
                    </div>

                    <div class="informative-box--content active" data-content="">
                        <span class="more"> <?= $plugin->text('dashboard.moreInformation'); ?> </span>
                       
                    </div>
                </button>
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
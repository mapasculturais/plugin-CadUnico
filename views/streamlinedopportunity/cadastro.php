<?php

use MapasCulturais\i;
use MapasCulturais\Entities\Registration;

$app = \MapasCulturais\App::i();
$plugin = $this->controller->plugin;
$config = $plugin->config;
$slug = $plugin->slug;


$this->jsObject['opportunityId'] = $config['opportunity_id'];

?>
<section class="lab-main-content cadastro">
    <header>
        <div class="intro-message">
            <div class="name"> <?= i::__('Olá', 'streamlined-opportunity') ?> <?= $niceName ? ", " . $niceName : "" ?>!
                <br>
                <?= i::__('Clique', 'streamlined-opportunity') ?> <a href="<?= $registrationUrl = $app->baseUrl; ?>"><?= i::__('aqui', 'streamlined-opportunity') ?> </a> <?= i::__('para retornar à página inicial', 'streamlined-opportunity') ?>
            </div>
        </div>
    </header>

    <div class="js-lab-item lab-item cadastro-options">
        <?php  if (count($registrations) < $limit) { ?>
            <div class="long-description">
                <?= i::__($config['registration_screen']['long_description'], 'streamlined-opportunity') ?>
            </div>

            <h2 class="featured-title">
                <?= i::__($config['registration_screen']['title']) ?>
            </h2>
        <?php } else {?>
            <h2 class="featured-title">
                <?= i::__($config['registration_screen']['title_application_summary']) ?>
            </h2>        
        <?php } ?>

        <div class="lab-form-filter opcoes-inciso">
          
            <?php
            
            $title = i::__($config['registration_screen']['description']);
            
            $agent_id = $app->user->profile->id;
            
            if (count($registrations) < $limit && $isRegistrationOpen)  {
            ?>
                <button onclick="location.href='<?= $this->controller->createUrl('novaInscricao', ['agent' => $agent_id]) ?>'" clickable id="option3" class="informative-box lab-option">
                    <div class="informative-box--icon">
                        <i class="fas fa-user"></i>
                    </div>

                    <div class="informative-box--title">
                        <h2><?= $title ?></h2>
                        <i class="fas fa-minus"></i>
                    </div>

                    <div class="informative-box--content active" data-content="">
                        <span class="more"> <?= i::__('Mais informações', 'streamlined-opportunity') ?> </span>
                       
                    </div>
                </button>
            <?php
            } 
            foreach ($registrations as $registration) {
                $registrationUrl = $this->controller->createUrl('formulario', [$registration->id]);
                switch ($registration->status) {
                        //caso seja nao enviada (Rascunho)
                    case Registration::STATUS_DRAFT:
                        $this->part('streamlinedopportunity/cadastro/application-draft',  ['registration' => $registration, 'registrationUrl' => $registrationUrl, 'niceName' => $niceName, 'registrationStatusName' => 'Cadastro iniciado']);
                        break;
                        //caso  tenha sido enviada
                    default:
                        $registrationStatusName = $summaryStatusName[$registration->status];
                        $this->part('streamlinedopportunity/cadastro/application-status',  ['registration' => $registration, 'registrationStatusName' => $registrationStatusName]);
                        break;
                }
            }
            ?>
        </div>

    </div><!-- End item -->
</section>
<?php
$app = \MapasCulturais\App::i();
$config = $app->plugins['StreamlinedOpportunity']->config;
$slug = $this->controller->plugin->$config['slug'];

use MapasCulturais\i;
use MapasCulturais\Entities\Registration;


$this->jsObject['opportunityId'] = $config['opportunity_id'];

?>
<section class="lab-main-content cadastro">
    <header>
        <div class="intro-message">
            <div class="name"> <?= i::__('Olá', 'streamlined-opportunity') ?> <?= $niceName ? ", " . $niceName : "" ?>!
                <br>
                <?= i::__('Clique', 'streamlined-opportunity') ?> <a href="<?= $registrationUrl = $app->createUrl('site'); ?>"><?= i::__('aqui', 'streamlined-opportunity') ?> </a> <?= i::__('para retornar à página inicial', 'streamlined-opportunity') ?>
            </div>
        </div>
    </header>

    <div class="js-lab-item lab-item cadastro-options">
        <h2 class="featured-title">
            <?= i::__('Selecione abaixo o benefício desejado', 'streamlined-opportunity') ?>
        </h2>

        <div class="lab-form-filter opcoes-inciso">
            <?php
            $title = i::__('Trabalhadoras e trabalhadores da Cultura', 'streamlined-opportunity');
            if (count($registrations) < $limite) {
            ?>
                <button onclick="location.href='<?= $this->controller->createUrl('individual') ?>'" clickable id="option3" class="informative-box lab-option">
                    <div class="informative-box--icon">
                        <i class="fas fa-user"></i>
                    </div>

                    <div class="informative-box--title">
                        <h2><?= $title ?></h2>
                        <i class="fas fa-minus"></i>
                    </div>

                    <div class="informative-box--content active" data-content="">
                        <span class="more"> <?= i::__('Mais informações', 'streamlined-opportunity') ?> </span>
                        <span class="content"><i><?= i::__('Texto cadastro.php', 'streamlined-opportunity') ?></i></span>
                    </div>
                </button>
            <?php
            } else if ($this->controller->config['msg_disabled'] != '') {
                $mensagemDisabled = $this->controller->config['msg_disabled'];
                $this->part('streamlinedopportunity/cadastro/inciso-disabled',  ['mensagem' => $mensagemDisabled, 'title' => $title]);
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
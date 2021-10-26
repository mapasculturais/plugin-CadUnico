<?php

use MapasCulturais\i;

$app = \MapasCulturais\App::i();
$plugin = $this->controller->plugin;
$config = $plugin->config;
$slug = $this->controller->plugin->slug;

$this->bodyProperties['ng-app'] = "entity.app";
$this->bodyProperties['ng-controller'] = "EntityController";
$this->jsObject['angularAppDependencies'][] = 'entity.module.opportunity';

$this->addEntityToJs($registration);
$this->addOpportunityToJs($registration->opportunity);
$this->addOpportunitySelectFieldsToJs($registration->opportunity);
$this->addRegistrationToJs($registration);
$this->includeAngularEntityAssets($registration);
$this->includeEditableEntityAssets();

$_params = [
    'entity'      => $registration,
    'opportunity' => $registration->opportunity,
    'slug' => $slug
]; ?>

<section id="lab-status" class="lab-main-content">

    <article class="main-content registration" ng-controller="OpportunityController">

        <div class="status-card status-<?= $registration->status ?>">
            <h2 class="status-card--title"><?= $registrationStatusMessage['title'] ?? ''; ?></h2>

            <?php if (!empty($justificativaAvaliacao) && sizeof($justificativaAvaliacao) != 0) : ?>
                <?php foreach ($justificativaAvaliacao as $message) : ?>
                    <?php if (is_array($message) && !empty($config['display_default_result'])) : ?>
                        <?php if(!$defaultText){?>
                        <?= nl2br(str_replace(array('\r\n', '\r', '\n'), "<br />", $message['message'])); ?>
                        <hr>
                        <?php }?>
                    <?php else : ?>
                        <?php if(!$defaultText){?>
                        <p><?= nl2br(str_replace(array('\r\n', '\r', '\n'), "<br />", $message)); ?></p>
                        <?php } else {?>
                            <p><?= nl2br(str_replace(array('\r\n', '\r', '\n'), "<br />", $evaluateDefault)); ?></p>
                        <?php }?>
                        <hr>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php else : ?>
                <hr>
            <?php endif; ?>
                <?php if($config['text_link_button_status'] && $config['text_button_status'] && $registration->status == 1){ ?>
                    <?=$config['text_button_status']?> <a href="<?php echo $app->createUrl($slug, 'cadastro'); ?>"><?=$config['text_link_button_status']?> </a>
                <?php } ?>
            <?php
            

            /**
             *
             * Exibe mensagem com informações sobre solicitação de recurso nas inscrições com status 2 (inválida) e 3 (não selecionada)
             *
             * Verifica se existe uma mensagem no campo `Mensagem de Recurso para o Status` da oportunidade.
             * Se não tiver, verifica na configuração `msg_appeal`.
             *
             */
            if (!$recursos && ($registration->status == 3 || $registration->status == 2)) {
                $statusRecurso = '';

                if ($registration->opportunity->getMetadata("{$slug}_status_history")) {
                    $statusRecurso = $registration->opportunity->getMetadata("{$slug}_status_history");
                } elseif (!empty($config['msg_appeal'])) {
                    $statusRecurso = $config['msg_appeal'];
                }

                if ($statusRecurso) {
            ?>
                    <hr>
                    <h2 class="status-card--title"><?= i::__('Você pode entrar com recurso', 'streamlined-opportunity') ?></h2>
                    <p class="status-card--content"><?= $statusRecurso; ?></p>
            <?php
                }
            } ?>

        </div><!-- /.status-card -->
        <?php
        if ($recursos) {
            foreach ($recursos as $recurso) {
                if (is_numeric($recurso->result)) {
                    $status = $recurso->result;
                } else if ($recurso->result ==  'homogada por recurso') {
                    $status = 10;
                } else {
                    $status = 0;
                }
        ?>
                <div class="status-card status-<?= $status ?>">
                    <p class="status-card--content"><?= $recurso->evaluationData->obs; ?></p>
                </div>
        <?php
            }
        }
        ?>

        <?php $this->applyTemplateHook('reason-failure', 'begin', [$_params]); ?>
        <?php $this->applyTemplateHook('reason-failure', 'end'); ?>

        <h1><?= i::__('', 'streamlined-opportunity') ?></h1>

        <?php $this->part('streamlinedopportunity/registration-single--header', $_params) ?>

        <?php $this->part('singles/registration-single--fields', $_params) ?>

        <div class="wrap-button">
            <a href="<?php echo $app->createUrl($slug, 'cadastro'); ?>" class="btn secondary"><?= i::__('Voltar para inscrição', 'streamlined-opportunity') ?></a>
        </div><!-- /.wrap-button -->

    </article>

</section>
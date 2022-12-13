<?php

use MapasCulturais\i;

$app = \MapasCulturais\App::i();
$config = $plugin->config;
$slug = $this->controller->plugin->slug;

$this->bodyProperties['ng-app'] = "entity.app";
$this->bodyProperties['ng-controller'] = "EntityController";
$this->jsObject['angularAppDependencies'][] = 'entity.module.opportunity';

$PreventSend      = $config['opportunities_disable_sending'];
$PreventSendMessages      = $config['message_disable_sending'];

$this->addEntityToJs($registration);
$this->addOpportunityToJs($registration->opportunity);
$this->addOpportunitySelectFieldsToJs($registration->opportunity);
$this->addRegistrationToJs($registration);
$this->includeAngularEntityAssets($registration);
$this->includeEditableEntityAssets();

$_params = [
    'plugin' => $plugin,
    'entity'      => $registration,
    'opportunity' => $registration->opportunity,
    'slug' => $slug,
    'opportunityId' => $registration->opportunity->id,
    'PreventSend' => $PreventSend,
    'PreventSendMessages' => $PreventSendMessages,
    'comfirmation' => false
]; ?>

<section id="lab-status" class="lab-main-content">

    <article class="main-content registration" ng-controller="OpportunityController">

        <div class="status-card status-<?= $registration->status ?>">
            <h2 class="status-card--title"><?= $registrationStatusMessage['title'] ?? ''; ?></h2>

            <?php if (!empty($justificativaAvaliacao) && sizeof($justificativaAvaliacao) != 0) : ?>
                <?php foreach ($justificativaAvaliacao as $message) : ?>
                    <?php if (is_array($message) && !empty($config['display_default_result'])) : ?>
                        <?= nl2br(str_replace(array('\r\n', '\r', '\n'), "<br />", $message['message'])); ?>
                        <hr>
                    <?php else : ?>
                        <p><?= nl2br(str_replace(array('\r\n', '\r', '\n'), "<br />", $message)); ?></p>
                        <hr>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php else : ?>
                <hr>
            <?php endif; ?>
           
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
                    <h2 class="status-card--title"><?= $plugin->text("status.text");?></h2>
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

        <h1><?= $plugin->text("status.opportunity")?></h1>

        <?php $this->part('cadunico/registration-single--header', $_params) ?>

        <?php $this->part('singles/registration-single--fields', $_params) ?>

        <div class="wrap-button">
            <a href="<?php echo $app->createUrl($slug, 'cadastro'); ?>" class="btn secondary"><?= $plugin->text("status.buttonBack") ?></a>
        </div><!-- /.wrap-button -->

    </article>

</section>
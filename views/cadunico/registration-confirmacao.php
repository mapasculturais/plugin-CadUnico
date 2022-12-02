<?php

use MapasCulturais\i;

$app = \MapasCulturais\App::i();
$plugin = $this->controller->plugin;
$config = $plugin->config;
$slug = $this->controller->plugin->slug;

$PreventSend      = $config['opportunities_disable_sending'];
$PreventSendMessages      = $config['message_disable_sending'];



$action = preg_replace("#^(\w+/)#", "", $this->template);

$this->bodyProperties['ng-app'] = "entity.app";
$this->bodyProperties['ng-controller'] = "EntityController";

$this->jsObject['angularAppDependencies'][] = 'entity.module.opportunity';

$this->addEntityToJs($entity);

$this->addOpportunityToJs($entity->opportunity);

$this->addOpportunitySelectFieldsToJs($entity->opportunity);

$this->addRegistrationToJs($entity);

$this->includeAngularEntityAssets($entity);
$this->includeEditableEntityAssets();

$_params = [
    'entity' => $entity,
    'action' => $action,
    'opportunity' => $entity->opportunity,
];
$opportunityId = $entity->opportunity->id;

?>
<article class="main-content registration" ng-controller="RegistrationFieldsController">

    <article>
        <?php $this->applyTemplateHook('form', 'begin'); ?>

        <?php $this->part('cadunico/registration-single--header', $_params) ?>

        <?php $this->part('singles/registration-single--fields', $_params) ?>

        <?php $this->applyTemplateHook('form', 'end'); ?>

        <?php if (in_array($opportunityId, $PreventSend)) { ?>
            <h2 class="registration-help">
                <strong>
                    <?= $PreventSendMessages[$opportunityId] ?? '' ?>
                </strong>
            </h2>
        <?php
        } else { ?>
            <p class="registration-help">
                <?= $plugin->text('confirmation.text') ?>
                <strong><?= $plugin->text('confirmation.alert') ?></strong>
            </p>
            <a class="btn btn-confirmar" ng-click="sendRegistration(false)" rel='noopener noreferrer'><?= $plugin->text('confirmation.buttonSend') ?></a>
        <?php
        } ?>

        <a href="<?= $this->controller->createUrl('formulario', [$entity->id]) ?>" class="btn secondary"><?= $plugin->text('confirmation.buttonEdit') ?></a>

    </article>
    <div ng-show="data.sent" style="display:none" id="modalAlert" class="modal">
        <!-- Modal content -->
        <div class="modal-content">
            <h2><?= $plugin->text('confirmation.modalTitle') ?></h2>
            <p class="text"><?= $plugin->text('confirmation.modalText') ?></p>
            <a href="<?= $this->controller->createUrl('status', [$entity->id]) ?>" class="btn js-confirmar"><?= $plugin->text('confirmation.modalConfirm') ?></a>
        </div>
    </div>

</article>

<script>
    $(window).ready(function() {
        $('.btn-confirmar').click(function() {
            $('#modalAlert').css('display', 'flex')
        });
    });
</script>
<?php
use MapasCulturais\i;

$app = \MapasCulturais\App::i();
$plugin = $this->controller->plugin;
$config = $plugin->config;
$slug = $this->controller->plugin->slug;

?>
<div ng-controller="RegistrationFieldsController">
<div class="registration-fieldset">
    <a ng-init="validateRegistration()" ng-click="validateRegistration()" class="btn btn-secondary btn-validate">Validar</a>
    <div class="errors-header" ng-if="numFieldErrors() > 0">
        <p class="errors-header-title"><?= $plugin->text('modal.error.title') ?></p>
        <p><?= $plugin->text('modal.error.subtitle') ?></p>
    </div>
    <div class="errors-header" ng-if="numFieldErrors() == 0">
        <p class="errors-header-title"><?= $plugin->text('modal.error.text-send') ?></p>
    </div>
    <div class="errors" ng-repeat="field in data.fields" ng-if="entityErrors[field.fieldName]">
        <a ng-click="scrollTo('wrapper-' + field.fieldName, 130)">
            {{field.title.replace(':', '')}}: <span class="errors-field" ng-repeat="error in entityErrors[field.fieldName]">{{error}} </span>
        </a>
    </div>
</div>

<div  id="modalAlert" class="modal" style="display: none;">
    <!-- Modal content -->
    <div class="modal-content">
        <h2 class="modal-content--title"><?= $plugin->text('modal.success.title') ?></h2>
        <p class="text"><?= $plugin->text('modal.success.subtitle') ?></p>
        <p class="text"><?= $plugin->text('modal.success.text') ?></p>
        <a href="<?= $this->controller->createUrl('confirmacao', [$entity->id]) ?>" ng-click="" class="btn btn-primary js-confirmar"><?= $plugin->text('modal.success.btn.text') ?></a>
    </div>
</div>

<script>
    $(window).ready(function () {
        $('.btn-validate').click(function () {
            $('#modalAlert').css('display', 'flex')
        });
    });
</script>
</div>
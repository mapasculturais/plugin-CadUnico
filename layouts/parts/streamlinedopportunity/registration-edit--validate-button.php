<?php
$app = \MapasCulturais\App::i();

?>
<div ng-controller="RegistrationFieldsController">
    <div class="registration-fieldset" ng-init="validateRegistration()">
        <a ng-click="saveRegistration(); validateRegistration();" class="btn btn-primary btn-validate"><?= $plugin->text('validate.ButtonSave') ?></a>
        <a ng-if="numFieldErrors() == 0" href="<?= $this->controller->createUrl('confirmacao', [$entity->id]) ?>" ng-click="" class="btn btn-primary js-confirmar"><?= $plugin->text('validate.ButtonReview') ?></a>
        <div class="errors-header" ng-if="numFieldErrors() > 0">
            <p class="errors-header-title"><?= $plugin->text('validate.error.title') ?></p>
            <p><?= $plugin->text('validate.error.subtitle') ?></p>
            <div class="errors" ng-repeat="field in data.fields" ng-if="entityErrors[field.fieldName]">
                <a ng-click="scrollTo('wrapper-' + field.fieldName, 130)">
                    {{field.title.replace(':', '')}}: <span class="errors-field" ng-repeat="error in entityErrors[field.fieldName]">{{error}} </span>
                </a>
            </div>
        </div>

        <div class="errors-header" ng-if="numFieldErrors() == 0" style="color: blue;">
            <p class="errors-header-title"><?= $plugin->text('validate.success.title') ?></p>
            <p><?= $plugin->text('validate.success.text') ?></p>
        </div>

    </div>
</div>

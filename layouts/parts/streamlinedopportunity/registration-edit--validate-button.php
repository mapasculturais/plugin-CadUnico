<?php
use MapasCulturais\i;

$app = \MapasCulturais\App::i();
$config = $app->plugins['StreamlinedOpportunity']->config;
$slug = $this->controller->plugin->slug;

?>
<div ng-controller="RegistrationFieldsController">
<div class="registration-fieldset">
    <a ng-init="validateRegistration()" ng-click="validateRegistration()" class="btn btn-secondary btn-validate">Validar</a>
    <div class="errors-header" ng-if="numFieldErrors() > 0">
        <p class="errors-header-title"><?= i::__('O cadastro não foi enviado!', 'streamlined-opportunity') ?></p>
        <p><?= i::__('Corrija os campos listados abaixo e valide seu formulário utilizando o botão Validar.', 'streamlined-opportunity') ?></p>
    </div>
    <div class="errors-header" ng-if="numFieldErrors() == 0">
        <p class="errors-header-title"><?= i::__('O cadastro ainda não foi enviado! Use o botão Validar para finalizar seu cadastro.', 'streamlined-opportunity') ?></p>
    </div>
    <div class="errors" ng-repeat="field in data.fields" ng-if="entityErrors[field.fieldName]">
        <a ng-click="scrollTo('wrapper-' + field.fieldName, 130)">
            {{field.title.replace(':', '')}}: <span class="errors-field" ng-repeat="error in entityErrors[field.fieldName]">{{error}} </span>
        </a>
    </div>
</div>

<div show="{{entityValidated}}" ng-show="entityValidated" id="modalAlert" class="modal">
    <!-- Modal content -->
    <div class="modal-content">
        <h2 class="modal-content--title"><?= i::__('Preenchimento Finalizado', 'streamlined-opportunity') ?></h2>
        <p class="text"><?= i::__('Agradecemos sua participação!', 'streamlined-opportunity') ?></p>
        <p class="text"><?= i::__('Antes de enviar a inscrição, releia atentamente os dados preenchidos e certifique-se que estão todos corretos. Você pode editar o formulário caso encontre alguma informação incorreta.', 'streamlined-opportunity') ?></p>
        <a href="<?= $this->controller->createUrl('confirmacao', [$entity->id]) ?>" ng-click="" class="btn btn-primary js-confirmar"><?php \MapasCulturais\i::_e("Revisar formulário"); ?></a>
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
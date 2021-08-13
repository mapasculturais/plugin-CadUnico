<?php
use MapasCulturais\i;

$app = \MapasCulturais\App::i();
$config = $app->plugins['StreamlinedOpportunity']->config;
$slug = $this->controller->plugin->slug;



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
    'opportunity' => $entity->opportunity
];
$opportunityId = $entity->opportunity->id;

?>
<article class="main-content registration" ng-controller="OpportunityController">

    <article>
        <?php $this->applyTemplateHook('form', 'begin'); ?>

        <?php $this->part('streamlinedopportunity/registration-single--header', $_params) ?>

        <?php $this->part('singles/registration-single--fields', $_params) ?>

        <?php $this->applyTemplateHook('form', 'end'); ?>

      
        <a href="<?= $this->controller->createUrl('formulario', [$entity->id]) ?>" class="btn secondary"><?php \MapasCulturais\i::_e("Editar formulário"); ?></a>

    </article>
    <div  ng-show="data.sent" style="display:none" id="modalAlert" class="modal">
        <!-- Modal content -->
        <div class="modal-content">
            <!-- <span class="close">&times;</span> -->
            <h2><?= i::__('Cadastro enviado com sucesso!', 'streamlined-opportunity') ?></h2>
            <p class="text"><?= i::__('Sua inscrição será analisada pelo comitê de curadoria e o resultado será informado por email. <br/>Você também pode acompanhar o andamento da análise através desse site.', 'streamlined-opportunity') ?></p>
            <a href="<?= $this->controller->createUrl('status', [$entity->id]) ?>" class="btn js-confirmar"><?= i::__('Acompanhar solicitação', 'streamlined-opportunity') ?></a>
        </div>
    </div>

</article>

<script>
    $(window).ready(function () {
        $('.btn-confirmar').click(function () {
            $('#modalAlert').css('display', 'flex')
        });
    });
</script>

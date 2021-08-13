<?php

use MapasCulturais\i;

$app = \MapasCulturais\App::i();
$config = $app->plugins['StreamlinedOpportunity']->config;
$slug = $this->controller->plugin->slug;

$action = preg_replace("#^(\w+/)#", "", $this->template);

$this->bodyProperties['ng-app'] = "entity.app";
$this->bodyProperties['ng-controller'] = "EntityController";
$this->jsObject['request']['controller'] = 'registration';
$this->jsObject['angularAppDependencies'][] = 'entity.module.opportunity';

$this->addEntityToJs($entity);

$this->addOpportunityToJs($entity->opportunity);

$this->addOpportunitySelectFieldsToJs($entity->opportunity);

$this->addRegistrationToJs($entity);

$this->includeAngularEntityAssets($entity);


$_params = [
    'entity' => $entity,
    'action' => $action,
    'opportunity' => $entity->opportunity
];

?>

<div id="editable-entity" class="clearfix sombra">
</div>
<article class="main-content registration" ng-controller="OpportunityController">
    <h1> <?= i::__('Solicitação de trabalhadora ou trabalhador da cultura', 'streamlined-opportunity') ?></h1>
    <?php $this->applyTemplateHook('form', 'begin'); ?>

    <?php $this->part('singles/registration-edit--header', $_params) ?>

    <?php $this->part('singles/registration-edit--fields', $_params) ?>

    <?php $this->part('streamlinedopportunity/registration-edit--validate-button', $_params) ?>

    <?php $this->applyTemplateHook('form', 'end'); ?>

</article>
<?php
/** 
 * @var StreamlinedOpportunity\Plugin $plugin 
 * @var MapasCulturais\Themes\BaseV1\Theme $this
 */

$plugin = $this->controller->plugin;
$config = $plugin->config;
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
    'opportunity' => $entity->opportunity,
    'plugin' => $plugin
];

?>

<div id="editable-entity" class="clearfix sombra">
</div>
<article class="main-content registration" ng-controller="OpportunityController">
    <h1> <?= $plugin->text('form.title') ?></h1>
    <?php if($text = $plugin->text('form.description')): ?>
        <div class="description"><?= $text ?></div>
    <?php endif; ?>

    <?php $this->applyTemplateHook('form', 'begin'); ?>

    <?php $this->part('singles/registration-edit--header', $_params) ?>

    <div ng-controller="RegistrationFieldsController">
        <?php $this->part('singles/registration-edit--fields', $_params) ?>

        <?php $this->part('streamlinedopportunity/registration-edit--validate-button', $_params) ?>
    </div>
    <?php $this->applyTemplateHook('form', 'end'); ?>

</article>
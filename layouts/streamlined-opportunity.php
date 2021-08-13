<?php 
$app = MapasCulturais\App::i();

$plugin = $app->plugins['StreamlinedOpportunity'];
$plugin->registerAssets();
?>
<?php $this->part("streamlinedopportunity/header"); ?>
<?php echo $TEMPLATE_CONTENT; ?>
<?php $this->part("streamlinedopportunity/footer"); ?>
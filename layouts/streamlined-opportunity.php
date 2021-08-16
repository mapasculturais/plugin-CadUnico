<?php 
$app = MapasCulturais\App::i();

$plugin = $this->controller->plugin;
$plugin->registerAssets();
?>
<?php $this->part("streamlinedopportunity/header"); ?>
<?php echo $TEMPLATE_CONTENT; ?>
<?php $this->part("streamlinedopportunity/footer"); ?>
<?php 
exit;
$app = MapasCulturais\App::i();

$plugin = $app->plugins['StreamlinedOpportunity'];
?>
<?php $this->part("streamlinedopportunity/header"); ?>
<?php echo $TEMPLATE_CONTENT; ?>
<?php $this->part("streamlinedopportunity/footer"); ?>
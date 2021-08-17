<?php
$plugin = $plugin ?? $this->controller->plugin;
$plugin->registerAssets();
$this->part("streamlinedopportunity/header", ["plugin" => $plugin]);
echo $TEMPLATE_CONTENT;
$this->part("streamlinedopportunity/footer", ["plugin" => $plugin]);
?>

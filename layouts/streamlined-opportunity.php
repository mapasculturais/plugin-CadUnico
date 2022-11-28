<?php
$plugin = $this->controller->plugin;

$this->part("streamlinedopportunity/header", ["plugin" => $plugin]);
echo $TEMPLATE_CONTENT;
$this->part("streamlinedopportunity/footer", ["plugin" => $plugin]);
?>

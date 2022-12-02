<?php

use CadUnico\Plugin;

$plugin = Plugin::getInstanceBySlug('cadunico');
$this->part("cadunico/header", ['plugin' => $plugin]);
echo $TEMPLATE_CONTENT;
$this->part("cadunico/footer", ['plugin' => $plugin]);
?>

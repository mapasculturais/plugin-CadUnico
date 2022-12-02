<?php

/**
 * Exibe o logo no cabeçalho
 */
$app = \MapasCulturais\App::i();
$config = $plugin->config;
$controller = $app->controller($config["slug"]);

$logo_institution = isset($config["logo_institution"]) ? $this->asset($config["logo_institution"], false) : $this->asset("cadunico/img/picture.png", false);
$logo_center = isset($config["logo_center"]) ? $this->asset($config["logo_center"], false) : $this->asset("cadunico/img/picture.png", false);
?>

<?php if ($logo_institution) {
    ?>
   <div class="logo-state">
        <img src="<?= $logo_institution ?>">
    </div>
    <?php
}?>

<?php if ($logo_center) {
    ?>
    <div class="logo">
      <a href="<?= $app->createUrl($config["slug"], "cadastro") ?>"> <img src="<?= $logo_center ?>"></a>
    </div>
    <?php
}?>

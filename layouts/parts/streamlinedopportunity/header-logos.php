<?php

/**
 * Exibe o logo no cabeçalho
 */
$app = \MapasCulturais\App::i();
$config = $plugin->config;
$controller = $app->controller($config["slug"]);

$logotipo_instituicao = isset($config["logotipo_instituicao"]) ? $config["logotipo_instituicao"] : $this->asset("streamlinedopportunity/img/picture.png", false);
$logotipo_central = isset($config["logotipo_central"]) ? $config["logotipo_central"] : $this->asset("streamlinedopportunity/img/picture.png", false);
?>

<?php if ($logotipo_instituicao) {
    ?>
   <div class="logo-state">
        <img src="<?= $logotipo_instituicao ?>">
    </div>
    <?php
}?>

<?php if ($logotipo_central) {
    ?>
    <div class="logo">
      <a href="<?= $app->createUrl($config["slug"], "cadastro") ?>"> <img src="<?= $logotipo_central ?>"></a>
    </div>
    <?php
}?>

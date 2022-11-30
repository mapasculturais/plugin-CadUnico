<?php

use MapasCulturais\i;

$app = \MapasCulturais\App::i();
$plugin = $plugin ?? $this->controller->plugin;
$config = $plugin->config;
$controller = $app->controller($config["slug"]);
$linkSuporte = isset($config["link_support"]) ? $config["link_support"] : '';
$termosECondicoes = isset($config["privacy_terms_conditions"]) ? $config["privacy_terms_conditions"] : $app->createUrl("auth", "", array("termos-e-condicoes"));
$logotipo = isset($config["logo_footer"]) ? $this->asset($config["logo_footer"], false) : $this->asset("streamlinedopportunity/img/picture.png", false); ?>

</section>

<?php if ($linkSuporte) {
    ?>
    <div class="support">
        <?= $plugin->text('footerNeedHelp'); ?> <a target="_blank" class="link" href="<?= $linkSuporte; ?> "> <?= $plugin->text('footer.ClickHere');?></a>
    </div>
<?php
} ?>

<footer id="main-footer">

    <?php if ($logotipo) {
    ?>
        <div class="logo-state">
            <img src="<?= $logotipo ?>">
        </div>
    <?php
    } ?>

    <?php if ($termosECondicoes) {
    ?>
        <a target="_blank" class="terms-conditions" href="<?= $termosECondicoes; ?> ">
            <?= $plugin->text('footerPrivacyPolicy'); ?>
        </a>
    <?php
    } ?>

    <div class="credits">
        <a href="https://github.com/mapasculturais/mapasculturais" target="_blank">
            <?= $plugin->text('footer.freeSoftware'); ?>
        </a>
        <span> por </span>

        <a href="https://hacklab.com.br/" class="hacklab" target="_blank" style="white-space: nowrap;">
            <?= $plugin->text('footer.Hacklab');?> <span>/</span>
        </a>

        <span> <?= $plugin->text('footer.Community') ?> </span>
    </div>
</footer>

<?php $this->bodyEnd(); ?>
</body>

</html>
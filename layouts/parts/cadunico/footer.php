<?php

$app = \MapasCulturais\App::i();
$config = $plugin->config;
$controller = $app->controller($config["slug"]);
$linkSuporte = isset($config["link_support"]) ? $config["link_support"] : '';
?>

</section>

<?php if ($linkSuporte) {
    ?>
    <div class="support">
        <?= $plugin->text('footer.needHelp'); ?> <a target="_blank" class="link" href="<?= $linkSuporte; ?> "> <?= $plugin->text('footer.clickHere');?></a>
    </div>
<?php
} ?>

<footer id="main-footer">
</footer>

<?php $this->bodyEnd(); ?>
</body>

</html>
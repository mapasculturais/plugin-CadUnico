<?php

use MapasCulturais\i;

$app = \MapasCulturais\App::i();
$config = $app->plugins['StreamlinedOpportunity']->config;
$controller = $app->controller($config['slug']);
$linkSuporte      = isset($config['link_suporte']) ? $config['link_suporte'] : '';
$termosECondicoes = isset($config['privacidade_termos_condicoes']) ? $config['privacidade_termos_condicoes'] : $app->createUrl('auth', '', array('termos-e-condicoes'));
$logotipo = isset($config['logotipo_instituicao']) ? $config['logotipo_instituicao'] : ''; ?>

</section>

<?php if ($linkSuporte) {
?>
    <div class="support">
        <?= i::__(' Precisa de ajuda?', 'streamlined-opportunity') ?> <a target="_blank" class="link" href="<?= $linkSuporte; ?> "> <?= i::__('Clique aqui', 'streamlined-opportunity') ?></a>
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
            <?= i::__('Politica de Privacidade e termos de condições de uso', 'streamlined-opportunity') ?>
        </a>
    <?php
    } ?>

    <div class="credits">
        <a href="https://github.com/mapasculturais/mapasculturais" target="_blank">
            <?= i::__('Software livre Mapas Culturais', 'streamlined-opportunity') ?>
        </a>
        <span> por </span>

        <a href="https://hacklab.com.br/" class="hacklab" target="_blank" style="white-space: nowrap;">
            <?= i::__('hacklab', 'streamlined-opportunity') ?> <span>/</span>
        </a>

        <span> <?= i::__('e comunidade', 'streamlined-opportunity') ?> </span>
    </div>
</footer>

<?php $this->bodyEnd(); ?>
</body>

</html>
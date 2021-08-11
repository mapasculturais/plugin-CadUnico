<?php
$app = \MapasCulturais\App::i();
$controller = $app->controller('streamlinedopportunity');
$config = $app->plugins['StreamlinedOpportunity']->config;
$linkSuporte      = isset($config['link_suporte']) ? $config['link_suporte'] : '';
$termosECondicoes = isset($config['privacidade_termos_condicoes']) ? $config['privacidade_termos_condicoes'] : $app->createUrl('auth', '', array('termos-e-condicoes'));
$logotipo = isset($config['logotipo_instituicao']) ? $config['logotipo_instituicao'] : '';?>

</section>

<?php if ($linkSuporte){
    ?>
    <div class="support">
        Precisa de ajuda? <a target="_blank" class="link" href="<?= $linkSuporte; ?> ">Clique aqui</a>
    </div>
    <?php
}?>

<footer id="main-footer">

    <?php if ($logotipo){
        ?>
       <div class="logo-state">
            <img src="<?= $logotipo ?>">
        </div>
        <?php
    }?>

    <?php if ($termosECondicoes){
        ?>
        <a target="_blank" class="terms-conditions" href="<?= $termosECondicoes; ?> ">
            Politica de Privacidade e termos de condições de uso
        </a>
        <?php
    }?>

    <div class="credits">
        <a href="https://github.com/mapasculturais/mapasculturais" target="_blank">
        Software livre Mapas Culturais
        </a> 
        <span> por </span> 

        <a href="https://hacklab.com.br/" class="hacklab" target="_blank" style="white-space: nowrap;">
            hacklab <span>/</span>
        </a>

        <span> e comunidade </span>
    </div>
    <?php  if($app->plugins['StreamlinedOpportunity']->config['zammad_enable']) {
                ?>
            <script src="<?= $app->plugins['StreamlinedOpportunity']->config['zammad_src_chat']; ?>"></script>
            <script>
                $(function() {
                new ZammadChat({
                    background: '<?= $app->plugins['StreamlinedOpportunity']->config['zammad_background_color']; ?>',
                    fontSize: '14px',
                    chatId: 1,
                    title: '<strong>Dúvidas?</strong> Fale conosco'

                });
                });
        </script>
         <style>.zammad-chat{
            z-index: 9999!important;
        }</style>
    
    <?php }?>
</footer>

<?php $this->bodyEnd(); ?>
</body>

</html>
<?php 
$url = $app->createUrl("autenticacao", "govbr");
?>
<div class="informative-box--icon">
    <i class="fas fa-user"></i>
</div>

<div class="informative-box--title">
    <img src="<?=$this->asset("img/sing-in-govbr.png", false)?>" style="width: 25%">
</div>

<div class="informative-box--content active" data-content="">
    <span class="more"> <?= $plugin->text('dashboard.moreInformation'); ?> </span>
</div>
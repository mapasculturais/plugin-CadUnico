
<?php
/** 
 * @var MapasCulturais\App $app
 * @var MapasCulturais\Themes\BaseV1\Theme $this
 * 
 * Variáveis requeridas:
 * @var CadUnico\Plugin $plugin 
 * @var MapasCulturais\Entities\Opportunity $opportunity
 */

$url = $this->controller->createUrl('novaInscricao', ['agent' => $app->user->profile->id])
?>

<button onclick="location.href='<?= $url ?>'" clickable id="option3" class="informative-box lab-option">
    <div class="informative-box--icon">
        <i class="fas fa-user"></i>
    </div>

    <div class="informative-box--title">
        <h2><?= $plugin->text('dashboard.button') ?: $opportunity->name ?></h2>
        <i class="fas fa-minus"></i>
    </div>

    <div class="informative-box--content active" data-content="">
        <span class="more"> <?= $plugin->text('dashboard.moreInformation'); ?> </span>

    </div>
</button>
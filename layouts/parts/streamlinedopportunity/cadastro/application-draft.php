<?php

use MapasCulturais\i;

/**
 * Carregada na tela inicial
 * Contém as informações resumidas do cadastro não enviado e link para tela de edição 
 */
?>

<div id="" class="lab-option draf-option">
    <a href="<?= $registrationUrl; ?>">
    <button class="informative-box lab-option has-status status-<?= $registration->status ?>">
        <div class="informative-box--status">
            <?php echo $registrationStatusName; ?>
        </div>
        <div class="informative-box--icon">
            <i class="fas fa-users"></i>
        </div>

        <div class="informative-box--title">
            <h2><?= i::__('Trabalhadoras e trabalhadores da Cultura', 'streamlined-opportunity') ?></h2>
            <i class="far fa-check-circle"></i>
        </div>

        <div class="informative-box--content" data-content="">
            <span class="more"> <?= i::__('Mais informações', 'streamlined-opportunity') ?> </span>
            <div class="content">
                <div class="item">
                    <span class="label"><?= i::__('Número:', 'streamlined-opportunity') ?></span> <?php echo $registration->number; ?> </br>
                </div>

                <?php if(!empty($registration->sentTimestamp)): ?>
                    <div class="item">
                        <span class="label"><?= i::__('Data do envio:', 'streamlined-opportunity') ?></span> <?php echo $registration->sentTimestamp ? $registration->sentTimestamp->format(\MapasCulturais\i::__('d/m/Y à\s H:i')) : ''; ?>. </br>
                    </div>
                <?php endif; ?>

                <div class="item">
                    <span class="label"><?= i::__('Responsável:', 'streamlined-opportunity') ?></span> <?php echo $registration->owner->name; ?> </br>
                </div>

                <div class="item">
                    <span class="label"><?= i::__('CPF:', 'streamlined-opportunity') ?></span> <?php echo $registration->owner->documento; ?>
                </div>
            </div>
        </div>
    </button>
        <div class="btn">
        <?= i::__('Continuar inscrição incompleta', 'streamlined-opportunity') ?>
        </div>
    </a>


</div>
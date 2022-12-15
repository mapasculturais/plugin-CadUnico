<?php

use MapasCulturais\i;

$plugin = $this->controller->plugin;
?>
<br>
<?php if($comfirmation):?>
<div class="registration-fieldset clearfix">
    <?php if (in_array($opportunityId, $PreventSend)) { ?>
        <h2 class="registration-help">
            <strong>
                <?= $PreventSendMessages[$opportunityId] ?? '' ?>
            </strong>
        </h2>
    <?php } else { ?>
        <p class="registration-help">
            <?= $plugin->text('confirmation.text') ?>
            <strong><?= $plugin->text('confirmation.alert') ?></strong>
        </p>

    <?php } ?>
</div>
<?php endif ?>

<?php if ($entity->projectName) : ?>
    <div class="registration-fieldset">
        <div class="label"><?= $plugin->text("status.projoctName"); ?></div>
        <h5> <?php echo $entity->projectName; ?> </h5>
    </div>
<?php endif; ?>
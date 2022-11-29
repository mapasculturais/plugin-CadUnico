<?php
use MapasCulturais\i;
$plugin = $this->controller->plugin;
?>

<div class="registration-fieldset clearfix">
    <h4><?= $plugin->text('status.sent.inscricao');?></h4>
    <div class="registration-id alignleft">
        <?php echo $entity->number ?>
    </div>
    <div class="alignright">
        <?php if ($entity->canUser('changeStatus')) : ?>
            <mc-select class="{{getStatusSlug(data.registration.status)}}" model="data.registration" data="data.registrationStatusesNames" getter="getRegistrationStatus" setter="setRegistrationStatus"></mc-select>
        <?php elseif ($opportunity->publishedRegistrations) : ?>
            <span class="status status-{{getStatusSlug(<?php echo $entity->status ?>)}}">{{getStatusNameById(<?php echo $entity->status ?>)}}</span>
        <?php endif; ?>

    </div>
</div>


<?php if ($entity->projectName) : ?>
<div class="registration-fieldset">
    <div class="label"><?= $plugin->text("status.sent.projoct.name"); ?></div>
    <h5> <?php echo $entity->projectName; ?> </h5>
</div>
<?php endif; ?>
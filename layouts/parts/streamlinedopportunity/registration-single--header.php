<?php
use MapasCulturais\i;

?>
<?php $sentDate = $entity->sentTimestamp; ?>
<?php if ($sentDate) : ?>
    <!-- <div class="alert success">
    <?php i::__('Inscrição enviada no dia', 'streamlined-opportunity'); ?>    
    <?php echo $sentDate->format(i::__('d/m/Y à\s H:i:s', 'streamlined-opportunity')); ?>
</div> -->
<?php endif; ?>

<h3 class="registration-header"><?= i::__('Confirmação da Inscrição', 'streamlined-opportunity') ?></h3>

<div class="registration-fieldset clearfix">
    <h4><?= i::__('Número da Inscrição', 'streamlined-opportunity') ?></h4>
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
        <div class="label"><?= i::__('Nome do Projeto', 'streamlined-opportunity') ?></div>
        <h5> <?php echo $entity->projectName; ?> </h5>
    </div>
<?php endif; ?>
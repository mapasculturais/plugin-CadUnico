<?php

use MapasCulturais\i;
?>

<div class="registration-fieldset">
    <h4><?=i::__('Declarações iniciais aceitas pelo proponente', 'streamlined-opportunity')?></h4>
    <div class="registration-list">
        <ul class="initial-statements">
            <?php foreach($terms as $value){?>
                <?php $result = explode("</strong>", $value);?>
                <li>
                    <small>
                        <i class="fas fa-check"></i>                    
                        <?=strip_tags($value)?>
                    </small>
                </li>
            <?php } ?>
        </ul>
    </div>
</div>
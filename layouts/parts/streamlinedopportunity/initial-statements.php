<?php

use MapasCulturais\i;
?>

<article>
    <h4><?=i::__('Declarações iníciais', 'streamlined-opportunity')?></h4>
    <ul class="initial-statements">
        <?php foreach($terms['items'] as $value){?>
            <?php $result = explode("</strong>", $value);?>
            <li>
                <small>
                    <i class="fas fa-check"></i>                    
                    <?=strip_tags($result[0])?>
                </small>
            </li>
        <?php } ?>
    </ul>
</article>
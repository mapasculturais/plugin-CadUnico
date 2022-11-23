<?php
/** 
 * @var StreamlinedOpportunity\Plugin $plugin 
 * @var MapasCulturais\Entities\Opportunity $opportunity
 * @var MapasCulturais\Themes\BaseV1\Theme $this
 */

use MapasCulturais\i;

$this->jsObject["registrationId"] = $registration_id;

?>
<section class="termos">
    <p class="termos--summary"><?= $plugin->tex ?> </p>
    <?php if($title = $plugin->text('terms.title')): ?>
        <h2><?= $title ?></h2>
    <?php endif; ?> 

    <div class="termos--list">
        <?php foreach ($plugin->terms as $term): ?>
        <label>
            <div class="term term--label">
                <span class="term--box"></span>
                <input type="checkbox" class="term--input" />
                <span class="termos--text">
                    <?= $term ?>
                </span>
            </div>
        </label> 
        <?php endforeach ?>
    </div>

    <nav class="termos--nav-terms">
        <button class="btn btn-large btn-lab js-btn"> <?= i::__('Continuar', 'streamlined-opportunity') ?></button>
    </nav>

    <div id="modalAlert" class="modal">
        <!-- Modal content -->
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2 class="modal-content--title title-modal"><?= i::__('Atenção!', 'streamlined-opportunity') ?></h2>
            <p>
                <?= $plugin->text('terms.help') ?>
            </p>
            <button id="btn-close" class="btn"> <?= i::__('OK', 'streamlined-opportunity') ?></button>
        </div>
    </div>

</section>

<script>
    var span = document.getElementsByClassName("close")[0];
    var modal = document.getElementById("modalAlert");
    var btnClose = document.getElementById("btn-close");
    var btnProsseguir = document.querySelector(".js-btn");

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == btnProsseguir) {
            goToNextPage();
        } else {
            if (modal.style.display == 'flex') {
                modal.style.display = "none";
            }
        }

    }
    // When the user clicks on <span> (x), close the modal
    span.onclick = function() {
        modal.style.display = "none";
    }

    btnClose.onclick = function() {
        modal.style.display = "none";
    }

    function goToNextPage() {
        var checkboxes = document.querySelectorAll('input[type="checkbox"]');
        var checkboxesChecked = document.querySelectorAll('input[type="checkbox"]:checked');

        if (checkboxes.length === checkboxesChecked.length) {
            //redirect to next page
            document.location = MapasCulturais.createUrl('<?= $plugin->slug ?>', 'aceitar_termos', [MapasCulturais.registrationId])
        } else {
            modal.style.display = "flex";
        }
    }
</script>

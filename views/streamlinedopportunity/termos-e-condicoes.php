<?php

use MapasCulturais\i;
use MapasCulturais\App;

$app = App::i();
$slug = $this->controller->plugin->slug;
$this->jsObject['registrationId'] = $registration_id;

?>
<section class="termos">
    <p class="termos--summay"><?= i::__('Para ser beneficiário do Programa “MS Cultura Cidadã”, o trabalhador da cultura deverá preencher, cumulativamente, os requisitos de elegibilidade a serem documentalmente comprovados no ato da inscrição, conforme previsto no Art. 2º da Lei Estadual nº 5.688, de 7 de julho de 2021 e Art. 9º do Decreto Estadual nº 15.728, de 14 de julho de 2021, e, conjuntamente, não poderá apresentar quaisquer das condições impeditivas previstas no Art. 3º da Lei Estadual.', 'streamlined-opportunity') ?> </p>

    <h2>
        <?= i::__('Termos e Condições', 'streamlined-opportunity') ?><br />

    </h2>

    <div class="termos--list">
        <div class="term">
            <span class="term--box"></span>
            <label class="term--label">
                <input type="checkbox" class="term--input" />
                <span class="termos--text">
                    <?= i::__('DECLARO SER RESIDENTE NO ESTADO DE MATO GROSSO DO SUL, CONFORME INCISO I DO ART. 2º DA LEI ESTADUAL Nº 5.688/2021, E ARTIGO 9º, INCISO II DO DECRETO ESTADUAL Nº 15.728/2021. ', 'streamlined-opportunity') ?>
                </span>
            </label>
        </div>
        <div class="term">
            <span class="term--box"></span>
            <label class="term--label">
                <input type="checkbox" class="term--input" />
                <span class="termos--text">
                    <?= i::__('DECLARO TER PARTICIPADO DA CADEIA PRODUTIVA DOS SEGMENTOS ARTÍSTICOS E CULTURAIS DO ESTADO DE MATO GROSSO DO SUL NOS 24 (VINTE E QUATRO) MESES IMEDIATAMENTE ANTERIORES À 19 DE MARÇO DE 2020, DATA DA EDIÇÃO DO DECRETO ESTDUAL Nº 15.396, CONFORME INCISO II DO ART. 2º DA LEI ESTADUAL Nº 5.688/2021, E ARTIGO 9º, INCISO III DO DECRETO ESTADUAL Nº 15.728/2021.', 'streamlined-opportunity') ?>
                </span>
            </label>
        </div>
        <div class="term">
            <span class="term--box"></span>
            <label class="term--label">
                <input type="checkbox" class="term--input" />
                <span class="termos--text">
                    <?= i::__('DECLARO QUE ESTOU CIENTE DE QUE, SERÁ CONCEDIDO APENAS 1 (UM) APOIO FINANCEIRO EMERGENCIAL POR FAMÍLIA, CONFORME ART. 1º, § 3º DA LEI Nº 5.688/2021 E ART. 9º, INCISO IV DO DECRETO Nº 15.728/2021:', 'streamlined-opportunity') ?> </span>
            </label>
        </div>
        <div class="term">
            <span class="term--box"></span>
            <label class="term--label">
                <input type="checkbox" class="term--input" />
                <span class="termos--text">
                    <?= i::__('DDECLARO QUE ESTOU CIENTE DE QUE, A PARTICIPAÇÃO NO PROGRAMA “MS CULTURA CIDADÃ” É CONDICIONADA À RENÚNCIA AO DIREITO DE FUTURA AÇÃO RELATIVA A EVENTUAIS INDENIZAÇÕES DECORRENTES DE MEDIDAS RESTRITIVAS IMPOSTAS EM RAZÃO DA EMERGÊNCIA EM SAÚDE PÚBLICA CAUSADA PELA PANDEMIA DO NOVO CORONAVÍRUS (COVID-19), BEM COMO À DESISTÊNCIA DE AÇÕES COM O MESMO TEOR JÁ PROPOSTAS EM FACE DO ESTADO, COM A CORRESPONDENTE RENÚNCIA AO DIREITO VEICULADO NA DEMANDA, CONFORME PARAGRAFO ÚNICO DO ART. 2º DA LEI ESTADUAL Nº 5.688/2021, E ARTIGO 9º, INCISO V DO DECRETO ESTADUAL Nº 15.728/2021.', 'streamlined-opportunity') ?>
                </span>
            </label>
        </div>
        <div class="term">
            <span class="term--box"></span>
            <label class="term--label">
                <input type="checkbox" class="term--input" />
                <span class="termos--text">
                    <?= i::__('PROGRAMAS FEDERAIS OU RESTRIÇÃO DE ACESSO, CASO JÁ BENEFICIADO, CONFORME ART. 9º, §2º DO DECRETO ESTADUAL Nº 15.728/2021.', 'streamlined-opportunity') ?> </span>
            </label>
        </div>
        <div class="term">
            <span class="term--box"></span>
            <label class="term--label">
                <input type="checkbox" class="term--input" />
                <span class="termos--text">
                    <?= i::__('DECLARO QUE NÃO POSSUO EMPREGO FORMAL ATIVO NA INICIATIVA PRIVADA, COM CONTRATO DE TRABALHO FORMALIZADO NOS TERMOS DA CONSOLIDAÇÃO DAS LEIS DO TRABALHO, CONFORME O INCISO I DO ART. 3º DA LEI ESTADUAL Nº 5.688/2021, E O § 3º DO ART. 10 DO DECRETO ESTADUAL Nº 15.728/2021.', 'streamlined-opportunity') ?> </span>
            </label>
        </div>
        <div class="term">
            <span class="term--box"></span>
            <label class="term--label">
                <input type="checkbox" class="term--input" />
                <span class="termos--text">
                    <?= i::__('DECLARO QUE NÃO SOU DETENTOR DE CARGO, EMPREGO OU FUNÇÃO PÚBLICOS, CONFORME INCISO II DO ART. 3º DA LEI ESTADUAL Nº 5.688/2021.', 'streamlined-opportunity') ?> </span>
            </label>
        </div>
        <div class="term">
            <span class="term--box"></span>
            <label class="term--label">
                <input type="checkbox" class="term--input" />
                <span class="termos--text">
                    <?= i::__('DECLARO QUE NÃO SOU TITULAR DE BENEFÍCIO PREVIDENCIÁRIO, CONFORME INCISO III DO ART. 3º  DA LEI ESTADUAL Nº 5.688/2021.', 'streamlined-opportunity') ?> </span>
            </label>
        </div>
        <div class="term">
            <span class="term--box"></span>
            <label class="term--label">
                <input type="checkbox" class="term--input" />
                <span class="termos--text">
                    <?= i::__('DECLARO QUE NÃO ESTOU RECEBENDO BENEFÍCIO DO SEGURO DESEMPREGO, CONFORME INCISO IV DO ART. 3º  DA LEI ESTADUAL Nº 5.688/2021.', 'streamlined-opportunity') ?> </span>
            </label>
        </div>
        <div class="term">
            <span class="term--box"></span>
            <label class="term--label">
                <input type="checkbox" class="term--input" />
                <span class="termos--text">
                    <?= i::__('DECLARO QUE ESTOU CIENTE DE QUE, EM CASO DE UTILIZAÇÃO DE QUALQUER MEIO ILÍCITO, IMORAL OU DECLARAÇÃO FALSA PARA A PARTICIPAÇÃO DESTE CREDENCIAMENTO, INCORRO NA PENALIDADE PREVISTA NO ARTIGO 299 DO DECRETO LEI Nº 2.848, DE 07 DE DEZEMBRO DE 1940 (CÓDIGO PENAL), ALÉM DE ENSEJAR A ADOÇÃO DAS MEDIDAS CABÍVEIS, NAS ESFERAS ADMINISTRATIVA E JUDICIAL.', 'streamlined-opportunity') ?></span>
            </label>
        </div>
        <div class="term">
            <span class="term--box"></span>
            <label class="term--label">
                <input type="checkbox" class="term--input" />
                <span class="termos--text">
                    <?= i::__('DECLARO QUE ESTOU CIENTE DA CONCESSÃO DAS INFORMAÇÕES POR MIM DECLARADAS NESTE FORMULÁRIO PARA PESQUISA E VALIDAÇÃO EM OUTRAS BASES DE DADOS OFICIAIS.', 'streamlined-opportunity') ?></span>
            </label>
        </div>
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
                <?= i::__('Você precisa aceitar todos os termos para continuar com a inscrição no auxílio emergencial da cultura.', 'streamlined-opportunity') ?>
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
            document.location = MapasCulturais.createUrl('<?= $slug ?>', 'aceitar_termos', [MapasCulturais.registrationId])
        } else {
            modal.style.display = "flex";
        }
    }
</script>
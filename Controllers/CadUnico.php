<?php

namespace CadUnico\Controllers;

use MapasCulturais\ApiQuery;
use MapasCulturais\i;
use MapasCulturais\App;
use MapasCulturais\Controller;
use MapasCulturais\Entities\Registration;
use CadUnico\Plugin;

/**
 * CadUnico Controller
 *
 * @property-read Registration $requestedEntity The Requested Entity
 * @property-read array $statusNames nomes dos status
 * @property-read mixed $config configuração do plugin
 */
class CadUnico extends \MapasCulturais\Controllers\Registration
{  

    /**
     * Instância do plugin
     *
     * @var \CadUnico\Plugin
     */
    protected $plugin;

    protected $_initiated = false;

    function __construct()
    {
        parent::__construct();
        $this->entityClassName = Registration::class;
    }

    /**
     * Retorna uma instância do controller
     * @param string $controller_id 
     * @return CadUnico 
     */
    static public function i(string $controller_id): Controller {
        
        $instance = parent::i($controller_id);
        $instance->init($controller_id);

        return $instance;
    }

    protected function init($controller_id) {
        if(!$this->_initiated) {
            $app = App::i();
            $this->plugin = Plugin::getInstanceBySlug($controller_id);
            $this->layout = $this->plugin->config['layout'];

            $slug = $this->plugin->getSlug();

            $app->hook("<<GET|POST|PUT|PATCH|DELETE>>({$slug}.<<*>>):before", function () {
                $registration = $this->getRequestedEntity();

                if (!$registration || !$registration->id) {
                    return;
                }

                $opportunity = $registration->opportunity;

                $this->registerRegistrationMetadata($opportunity);
            });
            $this->_initiated = true;
        }

    }

    function render($template, $data = [])
    {
        $this->plugin->registerAssets();
        parent::render($template, $data);
    }

    function getTemplatePrefix() {
        return 'cadunico';
    }

    function getConfig() {
        return $this->plugin->config;
    }
    
    /**
     * Retorna o valor com prefixo refenreciando o slug
     *
     * @param  mixed $value
     * @return string
     */
    function prefix($value){
        return $this->plugin->prefix($value);
    }

    /**
     * Retorna o array associativo com os numeros e nomes de status
     *
     * @return array
     */
    function getStatusNames(){
        $summaryStatusName = [
            Registration::STATUS_DRAFT => i::__('Rascunho', 'cad-unico'),
            Registration::STATUS_SENT => i::__('Em análise', 'cad-unico'),
            Registration::STATUS_APPROVED => i::__('Aprovado', 'cad-unico'),
            Registration::STATUS_NOTAPPROVED => i::__('Reprovado', 'cad-unico'),
            Registration::STATUS_WAITLIST => i::__('Recursos Exauridos', 'cad-unico'),
            Registration::STATUS_INVALID => i::__('Inválida', 'cad-unico'),
        ];
        return $summaryStatusName;
    }

   
    /**
     * Retorna Array com informações sobre o status de uma inscrição
     *
     * @return array
     */
    function getRegistrationStatusInfo(Registration $registration){
        $app = App::i();
        // retorna a mensagem de acordo com o status
        $getStatusMessages = $this->getStatusMessages();
        $registrationStatusInfo=[];
        $registrationStatusInfo['registrationStatusMessage'] = $getStatusMessages[$registration->status];
        // retorna as avaliações da inscrição
        $evaluations = $app->repo('RegistrationEvaluation')->findByRegistrationAndUsersAndStatus($registration);
        
        // monta array de mensagens
        $justificativaAvaliacao = [];

        if (in_array($registration->status, $this->config['display_default_result'])) {
            $justificativaAvaliacao[] = $getStatusMessages[$registration->status];
        }
        
        foreach ($evaluations as $evaluation) {

            if ($evaluation->getResult() == $registration->status) {
                
                if (in_array($evaluation->user->id, $this->config['evaluators_user_id']) && in_array($registration->status, $this->config['exibir_resultado_dataprev'])) {
                    // resultados do dataprev
                    $justificativaAvaliacao[] = $evaluation->getEvaluationData()->obs ?? '';
                } elseif (in_array($evaluation->user->id, $this->config['evaluators_generic_user_id']) && in_array($registration->status, $this->config['exibir_resultado_generico'])) {
                    // resultados dos avaliadores genericos
                    $justificativaAvaliacao[] = $evaluation->getEvaluationData()->obs ?? '';
                } 
                
                if (in_array($registration->status, $this->config['display_result_evaluators']) && !in_array($evaluation->user->id, $this->config['evaluators_user_id']) && !in_array($evaluation->user->id, $this->config['evaluators_generic_user_id'])) {
                    // resultados dos demais avaliadores
                    $justificativaAvaliacao[] = $evaluation->getEvaluationData()->obs ?? '';
                }

            }
            
        }
        $registrationStatusInfo['justificativaAvaliacao'] = $justificativaAvaliacao;
        return $registrationStatusInfo;
    }
    /**
     * Retorna array associativo com mensagens para cada status da inscrição
     *
     * @return array
     */
    function getStatusMessages(){
        $plugin = $this->plugin;
        $summaryStatusMessages = [
            //STATUS_SENT = 1 - Em análise
            '1' => [
                'title'   => $plugin->text('status.title'),
                'message'  => $plugin->text('status.message')
            ],
            //STATUS_INVALID = 2 - Inválida
            '2' => [
                'title'    => $plugin->text('status.invalid.title'),
                'message'  => $plugin->text('status.invalid.message')
            ],
            //STATUS_NOTAPPROVED = 3 - Reprovado
            '3' => [
                'title'    => $plugin->text('status.notapproved.title'),
                'message'  => $plugin->text('status.notapproved.message')
            ],
            //STATUS_APPROVED = 10 - Aprovado
            '10' => [
                'title'   => $plugin->text('status.approved.title'),
                'message' => $plugin->text('status.approved.message')
            ],
            //STATUS_WAITLIST = 8 - Recursos Exauridos
            '8' => [
                'title'   => $plugin->text('status.waitlist.title'),
                'message' => $plugin->text('status.waitlist.message')
            ]
        ];
        return $summaryStatusMessages;
    }

    function finish($data, $status = 200, $isAjax = false)
    {
        if (is_array($data)) {
            $data['redirect'] = 'false';
        } else if (is_object($data)) {
            $data->redirect = 'false';
        }
        parent::finish($data, $status, $isAjax);
    }

    /**
     * Redireciona o usuário para o formulário
     * 
     * rota: /{$slug}/registration/[?agent={agent_id}]
     * 
     * @return void
     */
    function GET_registration()
    {
        $this->requireAuthentication();

        $app = App::i();

        $app->view->includeEditableEntityAssets();

        if ($app->user->is('mediador')) {
            $agent = $this->createMediado();

            $app->redirect($this->createUrl('novaInscricao', ['agent' => $agent->id]));
            
        } else if (isset($this->data['agent']) && $this->data['agent'] != "" ) {
            $agent = $app->repo('Agent')->find($this->data['agent']);
        } else {
            $agent = $app->user->profile;
        }

        $metadata_key = $this->prefix("registration");

        // se ainda não tem inscrição
        if (!isset($agent->$metadata_key)) {
            /** 
             * verificar se o usuário tem mais de um agente, 
             * se tiver redireciona para a página de escolha de agente
             */
            $agent_controller = $app->controller('agent');

            $num_agents = $agent_controller->apiQuery([
                '@select' => 'id',
                '@permissions' => '@control',
                'type'=>'EQ(1)',
                '@count' => 1
            ]);                    
            if ($num_agents > 1) {
                // redireciona para a página de escolha de agente
                $app->redirect($this->createUrl('selecionar_agente',['tipo' => 1]));
            } else {

                // redireciona para a rota de criação de nova inscrição
                $app->redirect($this->createUrl('novaInscricao', ['agent' => $app->user->profile->id]));
            }
        }

        $app->redirect($this->createUrl('formulario', [$agent->$metadata_key]));
    }

    /**
     * Cria nova inscrição para o agente informado e redireciona para o formulário
     * 
     */
    function GET_novaInscricao()
    {   
        $this->requireAuthentication();
        if (!isset($this->data['agent'])) {
            // @todo tratar esse erro
            throw new \Exception(i::__('O parâmetro `agent` é obrigatório', 'cad-unico'));
        }

        $app = App::i();
        $agent = $app->repo('Agent')->find($this->data['agent']);
        //verifica se existe e se o agente owner é individual
          //se é coletivo cria um agente individual
        if ($agent->type->id == 2){
            $app->disableAccessControl();
            $agent = new \MapasCulturais\Entities\Agent();
            //@TODO: confirmar nome e tipo do Agente coletivo
            $agent->name = ' ';
            $agent->type = 1;
            $agent->save(true);
            $app->enableAccessControl();
        }
        if(!$agent || $agent->type->id != 1){
            // @todo tratar esse erro
            throw new \Exception(i::__('O tipo do agente deve ser individual', 'cad-unico'));
        }
        $agent->checkPermission('@control');

        $opportunity = $this->plugin->opportunity;

        $registrations = $app->repo('Registration')->findBy(['owner' => $agent->id, 'opportunity' => $opportunity->id]);

        if(count($registrations) >=  $this->plugin->config['limit']){
            $registration_id = $registrations[0]->id;
        }else{
            $registration = new \MapasCulturais\Entities\Registration;
            $registration->owner = $agent;
            $registration->opportunity = $opportunity;    
            $registration->save(true);
            $registration_id =  $registration->id;
        }

        $app->redirect($this->createUrl('formulario', [$registration_id]));
    }


    /**
     * Tela onde o usuário acompanha o status da inscrição
     *
     * @return void
     */
    function GET_status()
    {
        $app = App::i();

        $this->requireAuthentication();
        $registration = $this->requestedEntity;

        if(!$registration) {
            $app->pass();
        }
        if($registration->status == 0) {
            $app->redirect($this->createUrl('cadastro'));
        }
        $registration->checkPermission('view');

        // retorna a mensagem de acordo com o status
        $getStatusMessages = $this->getStatusMessages();
        $registrationStatusMessage = $getStatusMessages[$registration->status];
        
        // monta array de mensagens
        $justificativaAvaliacao = [];

        $recursos = [];

        // retorna informações de pagamento       
        $paymentMeta = $registration->metadata['secult_financeiro_raw'] ?? false;
            
        $payment = false;
        if ($paymentMeta && strpos($paymentMeta, 'Caso tenha algum problema com seu pagamento, entre em contato com o suporte') && strpos($paymentMeta, '"AVALIACAO":"selecionada"')) {
            $payment = true;
        }

        if($payment){
            // Verifica se é uma inscrição desbancarizada
            $accountCreationSecult = $registration->owner->metadata['account_creation'] ?? false;
            $branch = $registration->owner->payment_bank_branch ?? false;
            $secultRaw = json_decode($registration->metadata['secult_financeiro_raw'], true);
            if ($accountCreationSecult && $branch) {

                // Mensagem de Status para desbancarizados que possuem a conta criada pela SECULT.
                $messageStatus = 'O pagamento foi realizado. Para ter acesso ao auxílio, dirija-se até a agência ';
                $messageStatus .= $branch;
                $messageStatus .= ' para validar a abertura de sua conta pela SECULT. Lembre-se de levar RG, CPF e comprovante de residência.';
                $messageStatus .= '<br><br>';
                $messageStatus .= $secultRaw['OBSERVACOES'];
                $justificativaAvaliacao[] = $messageStatus;

            }else{

                $messageStatus = 'O pagamento do seu benefício foi realizado e já está disponível para saque na conta indicada no momento de sua inscrição.';
                $messageStatus .= '<br><br>';
                $messageStatus .= $secultRaw['OBSERVACOES'];            
                $justificativaAvaliacao[] = $messageStatus;
                
            }
            $registrationStatusMessage['title'] = 'Seu pagamento foi realizado com sucesso!!!';
        }else{

            // retorna as avaliações da inscrição
            $evaluations = $app->repo('RegistrationEvaluation')->findByRegistrationAndUsersAndStatus($registration);
                        
            if (in_array($registration->status, $this->config['display_default_result'])) {

                $justificativaAvaliacao[] = $getStatusMessages[$registration->status];

                foreach ($evaluations as $evaluation) {
                    $validacao = $evaluation->user->metadata['validator_for'] ?? null;

                    if ($validacao == 'recurso') {
                        $recursos[] = $evaluation;
                    }
    
                    // Verifica a configuração `not_display_results`
                    if (!in_array($evaluation->user->id, $this->config['not_display_results'])) {
                    
                        if (in_array($evaluation->user->id, $this->config['evaluators_user_id']) && in_array($registration->status, $this->config['exibir_resultado_dataprev'])) {
                            // resultados do dataprev
                            $avaliacao = $evaluation->getEvaluationData()->obs ?? '';
                            if (!empty($avaliacao)) {
                                if (($registration->status == 3 || $registration->status == 2) && substr_count($evaluation->getEvaluationData()->obs, 'Reprocessado')) {
    
                                    if ($this->config['msg_reprocessamento_dataprev']) {
                                        $justificativaAvaliacao[] = $this->config['msg_reprocessamento_dataprev'];
                                    } else {
                                        $justificativaAvaliacao[] = $avaliacao;
                                    }
                                    
                                } else {
                                    $justificativaAvaliacao[] = $avaliacao;
                                }
                            }
                        } elseif (in_array($evaluation->user->id, $this->config['evaluators_generic_user_id']) && in_array($registration->status, $this->config['exibir_resultado_generico'])) {
                            // resultados dos avaliadores genericos
                            $justificativaAvaliacao[] = $evaluation->getEvaluationData()->obs ?? '';
                        }

                        if (in_array($registration->status, $this->config['display_result_evaluators']) && !in_array($evaluation->user->id, $this->config['evaluators_user_id']) && !in_array($evaluation->user->id, $this->config['evaluators_generic_user_id'])) {
                            if (!in_array($evaluation, $recursos)) {
                                // resultados dos demais avaliadores
                                $justificativaAvaliacao[] = $evaluation->getEvaluationData()->obs ?? '';
                            }
                        }
                        
                    }


                }
    
            }

        }

        $avaliacoesRecusadas = $this->processaDeParaAvaliacoesRecusadas($registration);

        $this->render('status', [
            'plugin' => $this->plugin,
            'registration' => $registration, 
            'registrationStatusMessage' => $registrationStatusMessage, 
            'justificativaAvaliacao' => array_filter($justificativaAvaliacao),
            'recursos' => $recursos,
            'avaliacoesRecusadas' => $avaliacoesRecusadas,
        ]);
    }
    
    protected function setFlag($flag, $value) {
        $this->requireAuthentication();

        $app = App::i();

        $opportunity = $this->plugin->opportunity;

        if(!$opportunity) {
            $app->pass();
            die;
        }

        $opportunity->checkPermission('modify');

        $enabled = $this->prefix($flag);

        $opportunity->$enabled = $value ? '1' : '0';
        
        $opportunity->save(true);
    }
    
    /**
     * Habilita o plugin para a oportunidade
     */
    public function GET_enable ()
    {
        $this->setFlag('enabled', true);
    }
    
    /**
     * Desabilita o plugin para a oportunidade
     */
    public function GET_disable ()
    {
        $this->setFlag('enabled', false);
    }
    
    /**
     * Abilita o destaque na home
     */
    public function GET_enableFeatured ()
    {
        $this->setFlag('featured', true);
    }
    
    /**
     * Desabilita o destaque na home
     */
    public function GET_disableFeatured ()
    {
        $this->setFlag('featured', false);
    }


    /**
     * Renderiza o formulário da solicitação
     * 
     * rota: /{$slug}}/formulario/[{registration_id}]
     * 
     * @return void
     */
    function GET_formulario()
    {
        $app = App::i();
        $this->requireAuthentication();

        $registration = $this->getRequestedEntity();
        if($registration->status != Registration::STATUS_DRAFT){
            $app->redirect($this->createUrl('status', [$registration->id]));
        }

        $registration->checkPermission('modify');
        $now = new \DateTime('now');
        $notInTime = ($registration->opportunity->registrationFrom > $now || $registration->opportunity->registrationTo < $now );
        if ($notInTime){
            $app->redirect($this->createUrl('cadastro'));
        }
        if (!$registration->{$this->prefix("has_accepted_terms")}) {
            $app->redirect($this->createUrl('termos_e_condicoes', [$registration->id]));
        }
        
        // já é registrado no init do controller
        // $this->registerRegistrationMetadata($registration->opportunity);
        
        $app->view->includeEditableEntityAssets();

        $plugin = $this->plugin;
        $this->render('registration-edit', ['entity' => $registration, 'plugin' => $plugin]);
    }

    /**
     * Encaminha o usuário para a rota correta, de acordo com o tipo do usuário
     *
     * @return void
     */
    function GET_index()
    {
        $this->requireAuthentication();

        $app = App::i();

        $app->redirect($this->createUrl('cadastro'));

    }

    /**
     * Tela inicial para o proponente
     *
     * @return void
     */
    function GET_cadastro()
    {
        $this->requireAuthentication();
        
        $app = App::i();

        $summaryStatusName = $this->getStatusNames();

        $repo = $app->repo('Registration');
        
        $opportunity = $this->plugin->opportunity;
       
        $rs = new ApiQuery(Registration::class, [
            '@select' => 'id', 
            'opportunity' => "EQ({$opportunity->id})", 
            'status' => 'GTE(0)'
        ]);
        
        $registrations_ids = $rs->findIds();
        $registrations = $repo->findBy(['id' => $registrations_ids ]);
        $has_seal_govbr = $this->config['has_seal_govbr'];

        $this->render('cadastro', [
                'plugin' => $this->plugin,
                'opportunity' => $opportunity,
                'registrations' => $registrations,
                'summaryStatusName'=> $summaryStatusName,
                'has_seal_govbr' => $has_seal_govbr()
        ]);
    }

    /**
     * Página de aceite dos termos e condições
     * 
     * rota: /{$slug}/aceitar_termos/{id_inscricao}
     * 
     * @return void
     */
    function GET_termos_e_condicoes()
    {
        $this->requireAuthentication();
        
        
        if (!isset($this->data['id']) || $this->data['id'] == "" ) {
            // @todo tratar esse erro
            throw new \Exception();
        }

        $app = App::i();
        
        $registration = $app->repo('Registration')->find($this->data['id']);

        if (!$registration->id) {
            $app->pass();
        }
        
        $this->render('termos-e-condicoes', [
            'plugin' => $this->plugin,
            'opportunity' => $this->plugin->opportunity,
            'registration_id' => $registration->id
        ]);
    }

    /**
     * Aceitar os termos e condiçoes
     * 
     * rota: /{$slug}}/aceitar_termos/{id_inscricao}
     * 
     * @return void
     */
    function GET_aceitar_termos()
    {
        $this->requireAuthentication();
        $registration = $this->requestedEntity;
        $registration->checkPermission('modify');
        $registration->{$this->prefix("has_accepted_terms")} = true;
        $registration->save(true);
        $app = App::i();
        $app->redirect($this->createUrl('formulario', [$registration->id]));
    }

    function GET_selecionar_agente()
    {
        $this->requireAuthentication();
        $app = App::i();
        $tipo = 1;
        $agent_controller = $app->controller('agent');
        $agentsQuery = $agent_controller->apiQuery([
            '@select' => 'id,name,type,terms',
            '@permissions' => '@control',
            '@files' => '(avatar.avatarMedium):url',
            'type'=>'EQ(' . $tipo . ')',
        ]);
        $agents= [];
        foreach($agentsQuery as $agent){
            $agentItem         = new \stdClass();
            $agentItem->id     = $agent['id'];
            $agentItem->name   = $agent['name'];
            $agentItem->avatar = isset($agent['@files:avatar.avatarMedium']) ? $agent['@files:avatar.avatarMedium']['url']: '';
            $agentItem->type   = $agent['type']->name;
            $agentItem->areas  = $agent['terms']['area'];
            array_push($agents, $agentItem);
        }
        //Ordena o array de agents pelo name
        usort($agents, function($a, $b) {return strcmp($a->name, $b->name);});
        $this->data['agents'] = $agents;
        $this->render('selecionar-agente', $this->data);
    }

    /**
     * Confirmação de dados antes do envio do formulário
     * 
     * rota: /{$slug}/confirmacao/{id_inscricao}
     * 
     * @return void
     */
    function GET_confirmacao()
    {
        $app = App::i();
        $this->requireAuthentication();

        $plugin = $this->plugin;
        
        $registration = $this->getRequestedEntity();
        if($registration->status != Registration::STATUS_DRAFT){
            $app->redirect($this->createUrl('status', [$registration->id]));
        }
        if (!$registration->{$this->prefix("has_accepted_terms")}) {
            $app->redirect($this->createUrl('termos_e_condicoes', [$registration->id]));
        }
        $registration->checkPermission('control');
        $this->data['entity'] = $registration;

        $this->render('registration-confirmacao', $this->data, ['plugin' => $plugin]);
    }
    
    

    /**
     * 
     * Função para retornar todas as avalições das inscrições recusadas
     * passando pela config `de_para_avaliacoes`
     * 
     */
    function processaDeParaAvaliacoes ($registration){
        $app = App::i();
        $avaliacoes = $app->repo('RegistrationEvaluation')->findByRegistrationAndUsersAndStatus($registration);
        $configDePara = $this->config['de_para_avaliacoes'] ?? '';
        if(!empty($configDePara)){ 
            foreach($avaliacoes as $a){
                if ($a->result == 2 || $a->result == 3){
                    $novaAvaliacao = '';
                    $evaluationData = $a->getEvaluationData();
                    $obs = $evaluationData->obs;                    
                    foreach($configDePara as $key => $value) {
                        $pos = strpos($obs, $key);                        
                        if ($pos !== false) {
                            $novaAvaliacao .= $value . '. ';
                        }   
                    }          
                    if (!empty($novaAvaliacao)) {
                        $evaluationData->obs = $novaAvaliacao;
                        $a->setEvaluationData($evaluationData);
                    }  
                }                             
            }           
        }
        return $avaliacoes;
    }

    /**
     * 
     * Função para retornar apenas as avalições das inscrições recusadas
     * e que foram alteradas pela config `de_para_avaliacoes`
     * 
     */
    function processaDeParaAvaliacoesRecusadas($registration) {
        
        $app = App::i();
        $avaliacoes = $app->repo('RegistrationEvaluation')->findByRegistrationAndUsersAndStatus($registration);
        $configDePara = $this->config['de_para_avaliacoes'] ?? '';
        $avaliacoesProcessadas = [];
                         
        if(!empty($configDePara)){ 
            foreach($avaliacoes as $a){
                if ($a->result == 2 || $a->result == 3){                    
                    $evaluationData = $a->getEvaluationData();
                    $obs = $evaluationData->obs;           
                    foreach($configDePara as $key => $value) {
                        $pos = strpos($obs, $key);                        
                        if ($pos !== false && !in_array($value, $avaliacoesProcessadas)) {                            
                            $avaliacoesProcessadas[] = $value;
                        }
                    }
                }
            }
        }
        return $avaliacoesProcessadas;
    }
}

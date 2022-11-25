<?php

namespace StreamlinedOpportunity;

use DateTime;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMInvalidArgumentException;
use Doctrine\ORM\TransactionRequiredException;
use Doctrine\ORM\ORMException;
use Exception;
use MapasCulturais\App;
use MapasCulturais\Controller;
use MapasCulturais\i;
use MapasCulturais\Entities\Registration;
use MapasCulturais\Entities\Opportunity;
use Slim\Exception\Stop;

/**
 * @property-read String $slug slug configurado para o plugin
 * @property-read DateTime $fromDate data inicial das inscrições
 * @property-read DateTime $toDate data final das inscrições
 * @property-read integer $limit número máximo de inscrições por usuário
 * @property-read Controllers\StreamlinedOpportunity $controller controlador
 * 
 * @property-read Opportunity $opportunity oportunidade configurada
 * 
 * @package StreamlinedOpportunity
 */
class Plugin extends \MapasCulturais\Plugin
{

    /**
     * Oportunidade configurada
     * @var Opportunity
     */
    protected $_opportunity = null;

    /**
     * Instâncias do plugin ordenadas pelo slug
     * @var Plugin[]
     */
    protected static $instancesBySlug = [];

    /**
     * Instâncias do plugin ordenadas pelo id da oportunidade
     * @var Plugin[]
     */
    protected static $instancesByOpportunity = [];

    public function __construct(array $config = [])
    {
        $required_configs = [
            'slug' => i::__('A chave de configuração "slug" é obrigatória'),
            'opportunity_id' => i::__('A chave de configuração "opportunity_id" é obrigatória'),
        ];
        
        foreach($required_configs as $key => $message) {
            if(!isset($config[$key])) {
                throw new \Exception('StreamlinedOpportunity: ' . $message);
            }
        }

        $slug = $config['slug'];
        $opportunity_id = $config['opportunity_id'];

        
        self::$instancesBySlug[$slug] = $this;
        self::$instancesByOpportunity[$opportunity_id] = $this;

        $PREFIX = strtoupper($slug);

        /*
        ENABLED_STREAM_LINED_OPPORTUNITY => {$slug}_ENABLED
        demais somente adiciona o prefixo
        */
        $config += [
            // true habilita o plugin false desabilita
            'enabled' => env("{$PREFIX}_ENABLED", false),
            
            'redirect_login_enabled' => env("{$PREFIX}_REDIRECT_LOGIN_ENABLED", false),

            // Define a data e horario que as inscrições serão liberadas. Se não definido, utiliza a data da oportunidade.
            'registrations.from' => env("{$PREFIX}_REGISTRATIONS_FROM", null),

            // Define a data e horario que as inscrições serão encerradas. Se não definido, utiliza a data da oportunidade.
            'registrations.to' => env("{$PREFIX}_REGISTRATIONS_TO", null),

            // Opportunidade configurada no StreamLinedOpportunity
            'opportunity_id' => env("{$PREFIX}_OPPORTUNITY_ID", null),

            // número máximo de inscrições por usuário
            'limit' => env("{$PREFIX}_LIMIT", 1), 

            'initial_statement_enabled' => false,

            /* CONFIGURAÇÕES DE INTERFACE */
            // layout a ser utilizado como "moldura" das páginas
            'layout' => "streamlined-opportunity",

            /** ESTILOS INSERIDOS NAS ROTAS DOS PLUGINS */
            // configuração das variáveis das cores
            'styles:root' => [
                '--header-background' => env("{$PREFIX}_STYLES_HEADER_BG",'#a6e9a0'),
                '--header-color' => env("{$PREFIX}_STYLES_HEADER_COLOR",'#ffffff'),
            
                /* Footer */
                '--footer-background' => env("{$PREFIX}_STYLES_FOOTER_BG",'#a6e9a0'),
                '--footer-color' =>  env("{$PREFIX}_STYLES_FOOTER_COLOR",'black'),
            
                /* COMPONENTS */
                /* Button */
                '--primary-button-bg-color' => env("{$PREFIX}_STYLES_BUTTON_PRIMARY_BG",'#3275B6'),
                '--primary-button-txt-color' => env("{$PREFIX}_STYLES_BUTTON_PRIMARY_COLOR",'#ffffff'),

                '--secondary-button-bg-color' => env("{$PREFIX}_STYLES_BUTTON_SECONDARY_BG",'#666666'),
                '--secondary-button-txt-color' => env("{$PREFIX}_STYLES_BUTTON_SECONDARY_COLOR",'#ffffff'),
            
                /* Status Cards */
                '--status-1-background' => env("{$PREFIX}_STYLES_STATUS_CARD_1",'#9565D2'),
                '--status-2-background' => env("{$PREFIX}_STYLES_STATUS_CARD_2",'#cc0033'),
                '--status-3-background' => env("{$PREFIX}_STYLES_STATUS_CARD_3",'#cc0033'),
                '--status-8-background' => env("{$PREFIX}_STYLES_STATUS_CARD_8",'#666666'),
                '--status-10-background' => env("{$PREFIX}_STYLES_STATUS_CARD_10",'#B4BA00'),
            
                /* Informative boxes - check informative-box.scss for more info */
                '--box-status-1-background' => env("{$PREFIX}_STYLES_STATUS_INFO_1",'#9565D2'),
                '--box-status-2-background' => env("{$PREFIX}_STYLES_STATUS_INFO_2",'#666666'),
                '--box-status-3-background' => env("{$PREFIX}_STYLES_STATUS_INFO_3",'#C60931'),
                '--box-status-8-background' => env("{$PREFIX}_STYLES_STATUS_INFO_8",'#666666'),
                '--box-status-10-background' => env("{$PREFIX}_STYLES_STATUS_INFO_10",'#B4BA00'),
            
                /* The "default" stands for non status boxes (hasn't status-% class) */
                '--box-default-background' => env("{$PREFIX}_STYLES_BG",'#ffffff'),
                '--box-default-icon-color' => env("{$PREFIX}_STYLES_ICON_COLOR",'#3275B6'),
                '--box-default-status-background' => env("{$PREFIX}_STYLES_STATUS_BG",'#3275B6'),
                '--box-default-status-text' => env("{$PREFIX}_STYLES_STATUS_COLOR",'#ffffff'),
            ],

            // estilos adicionais para incluir nas rotas do plugin
            'styles' => "",

            // destacar a oportunidade na home?
            'featured' =>  env("{$PREFIX}_FEATURED", false),

            'featured.hook' => env("{$PREFIX}_FEATURED_HOOK", 'template(site.index.home-search-form):begin'),

            // template part do destaque da home
            'featured.part' => env("{$PREFIX}_FEATURED_PART", 'streamlinedopportunity/home-featured'),

            // url da imagem do destaque da home
            'featured.imageUrl' => '',

            
            /* TEXTOS E DEMAIS COMPONENTES DE INTERFACE */
            'texts' => [
                /* TEXTOS DO DASHBOARD */
                'dashboard.title' => i::__('Para se inscrever clique no botão abaixo', 'streamlined-opportunity'),
                'dashboard.description' => '', // se não definida, usará a descrição curta da oportunidade
                'dashboard.button' => '', // se não definida, usará o nome da oportunidade
                'dashboard.applicationSummaryTitle' => i::__('Resumo da inscrição', 'streamlined-opportunity'),

                /* TEXTOS DA TELA DO FORMULÁRIO */
                'form.title' => 'Formulário de inscrição no Cadastro Único da Cultura',
                'form.description' => '',

                /* TEXTOS DO DESTAQUE DA HOME */
                'home.featuredTitle' => '',
                'home.featuredText' => '',
                'home.featuredButton' => i::__('Clique aqui para se inscrever', 'streamlined-opportunity'),

                /* TERMOS E CONDIÇÕES */
                'terms.intro' => env("{$PREFIX}_TERMS_INTRO", ''),
                'terms.title' => env("{$PREFIX}_TERMS_TITLE", i::__('Termos e Condições', 'streamlined-opportunity')),
                'terms.help' => env("{$PREFIX}_TERMS_HELP", i::__('Você precisa aceitar todos os termos para prosseguir com a inscrição', 'streamlined-opportunity')),

                // STATUS_SENT = 1
                'status.sent.title' => env("{$PREFIX}_STATUS_SENT_TITLE", i::__('Sua inscrição está em análise', 'streamlined-opportunity')),
                'status.sent.message' => env("{$PREFIX}_STATUS_SENT_MESSAGE", i::__('Consulte novamente em outro momento.', 'streamlined-opportunity')),
                
                // STATUS_INVALID = 2
                'status.invalid.title' => env("{$PREFIX}_STATUS_INVALID_TITLE", i::__('Sua inscrição não foi aprovada', 'streamlined-opportunity')),
                'status.invalid.message' => env("{$PREFIX}_STATUS_INVALID_MESSAGE", i::__('Sua inscrição foi analisada e não foi aprovada.', 'streamlined-opportunity')),

                // STATUS_NOTAPPROVED = 3
                'status.notapproved.title' => env("{$PREFIX}_STATUS_NOTAPPROVED_TITLE", i::__('Sua inscrição não foi aprovada', 'streamlined-opportunity')),
                'status.notapproved.message' => env("{$PREFIX}_STATUS_NOTAPPROVED_MESSAGE", i::__('Sua inscrição foi analisada e não foi aprovada.', 'streamlined-opportunity')),

                //STATUS_WAITLIST = 8
                'status.waitlist.title' => env("{$PREFIX}_STATUS_WAITLIST_TITLE", i::__('Sua inscrição foi validada.', 'streamlined-opportunity')),
                'status.waitlist.message' => env("{$PREFIX}_STATUS_WAITLIST_MESSAGE", i::__('Inscrição suplente.', 'streamlined-opportunity')),

                //STATUS_WAITLIST = 10
                'status.approved.title' => env("{$PREFIX}_STATUS_APPROVED_TITLE", i::__('Sua inscrição foi aprovada.', 'streamlined-opportunity')),
                'status.approved.message' => env("{$PREFIX}_STATUS_APPROVED_MESSAGE", i::__('Sua inscrição foi analisada e foi aprovada.', 'streamlined-opportunity')),

            ],

            /*TERMOS E CONDIÇÕES */
            "terms" => json_decode(env("{$PREFIX}_TERMS_JSON", '["Termo 1", "Edite a configuração do plugin"]')),

            // AVALIAÇÕES E RESULTADOS
            'not_display_results' => (array) json_decode(env("{$PREFIX}_NAO_EXIBIR_RESULTADOS', '[]")),
            'evaluators_user_id' => (array) json_decode(env("{$PREFIX}_AVALIADORES_DATAPREV_USER_ID", '[]')),
            'evaluators_generic_user_id' => (array) json_decode(env("{$PREFIX}_AVALIADORES_GENERICOS_USER_ID", '[]')),
            'display_result_evaluators' => (array) json_decode(env("{$PREFIX}_EXIBIR_RESULTADO_AVALIADORES", '["2", "3", "10"]')),

            // só consolida a a homologaćão se todos as validaćões já tiverem sido feitas
            'consolidation_requires_validations' => (array) json_decode(env('HOMOLOG_REQ_VALIDACOES', '[]')),

            // STATUS_SENT = 1
            'text_button_status' => null,
            'text_link_button_status' => null,

            'logo_institution' => env("$PREFIX}_LOGO_INSTUCTION", ''),
            'logo_footer' => env("$PREFIX}_LOGO_FOOTER", ''),
            'logo_center' => env("$PREFIX}_LOGO_CENTER", ''),
            'privacy_terms_conditions' => env("$PREFIX}_PRIVACY_TERMS", null),
            'link_support' => env("$PREFIX}_LINK_SUPPORT", null),
            'link_support_footer' => env("{$PREFIX}_LINK_SUPORTE_FOOTER", null),
            'display_default_result' => (array) json_decode(env("{$PREFIX}_DISPLAY_DEFAULT_RESULT", '["1", "2", "3", "8", "10"]')),
            'msg_appeal' => env("{$PREFIX}_MESSAGE_APPEAL", ''),
            'opportunities_disable_sending' => (array) json_decode(env("{$PREFIX}_OPPORTUNITIES_DISABLE_SENDING", '[]')),
            'message_disable_sending' => (array) json_decode(env("{$PREFIX}_MESSAGE_DISABLE_SENDING", '[]')),
          

            /*EMAIL DE CONFIRMAÇÃO DE INSCRIÇÃO */
            "email_confirm_registration" => [
                'project_name' => env("$PREFIX}_PROJECT_NAME_EMAIL_CONFIRM_REGISTRATION", ''),
                'status_title' => env("$PREFIX}_STATUS_TITLE_EMAIL_CONFIRM_REGISTRATION", ''),
                'url_image_body' => env("$PREFIX}_IMAGE_BODY_CONFIRM_REGISTRATION", ''),
                'subject' => env("$PREFIX}_SUBJECT_EMAIL_CONFIRM_REGISTRATION", '')
            ],

            /**EMAIL DURANTE TROCA DE STATUS DA INSCRIÇÃO */
            "email_alter_status" => [
                "url_image_body" => env("{$PREFIX}EMAIL_ALTER_STATUS_IMAGE_BODY", ""),
                "project_name" => env("{$PREFIX}EMAIL_ALTER_STATUS_PROJECT", ""),
                "subject" => env("{$PREFIX}EMAIL_ALTER_STATUS_SUBJECT", ""),
                "send_email_status" => ['10','3','2'],
                "messageDefaultNoSendEmail" => "",
                "message_appeal" => [
                        'title' => env("{$PREFIX}MESSAGE_APPEAL_TITLE", ""),
                        'message' => env("{$PREFIX}MESSAGE_APPEAL_MESSAGE", ""),
                ],
                "message_status" => [
                    10 => [
                        'title' => env("{$PREFIX}SELECTED_STATUS_MESSAGE_TITLE", ""),
                        'message' => [
                            'part1' => env("{$PREFIX}SELECTED_STATUS_MESSAGE_MESSAGE_1", ""),
                            'part2' => env("{$PREFIX}SELECTED_STATUS_MESSAGE_MESSAGE_2", ""),
                            'part3' => env("{$PREFIX}SELECTED_STATUS_MESSAGE_MESSAGE_3", ""),
                            'part4' => env("{$PREFIX}SELECTED_STATUS_MESSAGE_MESSAGE_4", ""),
                            'part5' => env("{$PREFIX}SELECTED_STATUS_MESSAGE_MESSAGE_4", ""),
                            'part6' => env("{$PREFIX}SELECTED_STATUS_MESSAGE_MESSAGE_4", ""),
                            'part7' => env("{$PREFIX}SELECTED_STATUS_MESSAGE_MESSAGE_4", ""),

                        ],
                        'complement' => env("{$PREFIX}SELECTED_STATUS_MESSAGE_COMPLEMENT", ""),
                        'has_appeal' => env("{$PREFIX}SELECTED_STATUS_MESSAGE_HAS_APPEAL", false),
                    ],
                    3 => [
                        'title' => env("{$PREFIX}IVALID_STATUS_MESSAGE_TITLE", ""),
                        'message' => [
                            'part1' => env("{$PREFIX}SELECTED_STATUS_MESSAGE_MESSAGE_1", ""),
                            'part2' => env("{$PREFIX}SELECTED_STATUS_MESSAGE_MESSAGE_2", ""),
                            'part3' => env("{$PREFIX}SELECTED_STATUS_MESSAGE_MESSAGE_3", ""),
                            'part4' => env("{$PREFIX}SELECTED_STATUS_MESSAGE_MESSAGE_4", ""),
                            'part5' => env("{$PREFIX}SELECTED_STATUS_MESSAGE_MESSAGE_5", ""),
                            'part6' => env("{$PREFIX}SELECTED_STATUS_MESSAGE_MESSAGE_6", ""),
                            'part7' => env("{$PREFIX}SELECTED_STATUS_MESSAGE_MESSAGE_7", ""),
                        ],
                        'complement' => env("{$PREFIX}IVALID_STATUS_MESSAGE_COMPLEMENT", ""),
                        'has_appeal' => env("{$PREFIX}IVALID_STATUS_MESSAGE_HAS_APPEAL", true),
                    ],
                    2 => [
                        'title' => env("{$PREFIX}NO_SELECTED_STATUS_MESSAGE_TITLE", ""),
                        'message' => [
                            'part1' => env("{$PREFIX}SELECTED_STATUS_MESSAGE_MESSAGE_1", ""),
                            'part2' => env("{$PREFIX}SELECTED_STATUS_MESSAGE_MESSAGE_2", ""),
                            'part3' => env("{$PREFIX}SELECTED_STATUS_MESSAGE_MESSAGE_3", ""),
                            'part4' => env("{$PREFIX}SELECTED_STATUS_MESSAGE_MESSAGE_4", ""),
                            'part5' => env("{$PREFIX}SELECTED_STATUS_MESSAGE_MESSAGE_5", ""),
                            'part6' => env("{$PREFIX}SELECTED_STATUS_MESSAGE_MESSAGE_6", ""),
                            'part7' => env("{$PREFIX}SELECTED_STATUS_MESSAGE_MESSAGE_7", ""),
                        ],
                        'complement' => env("{$PREFIX}NO_SELECTED_STATUS_MESSAGE_COMPLEMENT", ""),
                        'has_appeal' => env("{$PREFIX}NO_SELECTED_STATUS_MESSAGE_HAS_APPEAL", true),
                    ],
                    'noSendEmail' => [
                        "specialCase" => []
                    ],

                ]
            ]
        ];

        parent::__construct($config);
    }
    
    /**
     * Retorna a instância do streamLinedOpportunity com referência ao slug
     *
     * @param  string $slug
     * @return Plugin
     */
    static function getInstanceBySlug(string $slug)
    {
        if (!isset(self::$instancesBySlug[$slug])) {
            throw new Exception(i::__("Instância do plugin StremlinedOpportunity não encontrada: ") . $slug);
        }

        return self::$instancesBySlug[$slug];
    }

    
    /**
     * Retorna a instância do streamLinedOpportunity com referência a oportunidade
     *
     * @param int $opportunity_id
     * @return Plugin
     */
    public static function getInstanceByOpportunityId(int $opportunity_id)
    {
        if (!isset(self::$instancesByOpportunity[$opportunity_id])) {
            throw new Exception(i::__("Instância do plugin StremlinedOpportunity não encontrada: ") . $opportunity_id);
        }

        return self::$instancesByOpportunity[$opportunity_id];

    }

    public function registerAssets()
    {
        $app = App::i();

        // enqueue scripts and styles
        $app->view->enqueueScript('app', 'streamlinedopportunity', 'streamlinedopportunity/app.js');
        // $app->view->enqueueStyle('app', 'app-customization', 'streamlinedopportunity/customization.css');
        $app->view->enqueueStyle('app', 'app', 'streamlinedopportunity/app.css');

        $plugin = $this;

        $app->hook('mapasculturais.styles', function() use ($app, $plugin) {
            $app->view->part('streamlinedopportunity/styles', ['plugin' => $plugin]);
        });
    }

    public function _init()
    {
        $app = App::i();

        $plugin = $this;
        $config = $plugin->config;

        if (!$config['enabled']) {
            return;
        }

        $opportunity = $this->opportunity;

        // Insere o nome dos avaliadores na lista de inscritos
        $app->hook('opportunity.registrations.reportCSV', function(\MapasCulturais\Entities\Opportunity $opportunity, $registrations, &$header, &$body) use ($app) {
            
            $evaluations = $app->repo('RegistrationEvaluation')->findBy(['registration' => $registrations]);

            foreach ($evaluations as $evaluation) {
                $avaliadores[$evaluation->registration->number] = $evaluation->user->profile->name;
            }
            
            $header[] = 'Avaliadores da inscrição';

            foreach($body as $i => $line){
                $body[$i][] = $avaliadores[$line[0]] ?? null;
            }
        });

        /**Insere declarações iniciais na ficha de inscrição para quem tem controle da inscrição */
        $app->hook("template(registration.view.form):end", function() use ($plugin){
            $registration = $this->controller->requestedEntity;
            if($plugin->config['initial_statement_enabled'] && $registration->canUser('@control') && $plugin->isStreamLinedOpportunity($registration->opportunity)){
                  /** @var \MapasCulturais\Theme $this */
                $this->enqueueStyle('app', 'streamlined-opportunity', 'css/streamlinedopportunity.css');
                $this->part('streamlinedopportunity/initial-statements', ['terms' => $plugin->config['terms']]);
            }
        });
        
        /**
         * só consolida as avaliações para "selecionado" se tiver acontecido as validações de algum validador
         * 
         * @TODO: implementar para método de avaliaçào documental
         */
        $app->hook('entity(Registration).consolidateResult', function(&$result, $caller) use($plugin, $app) {

            
            $opportunities_id = $plugin->config['opportunity_id'];
            
            if ($this->opportunity->id != $opportunities_id) {
                return;
            }
            
            // só aplica o hook para usuários homologadores
            if ($caller->user->validator_for) {
                return;
            }

            $evaluations = $app->repo('RegistrationEvaluation')->findBy(['registration' => $this, 'status' => 1]);

            $result = $caller->result;
                        
            foreach ($evaluations as $eval) {
                if ($eval->user->validator_for) {
                    continue;
                }

                if(intval($eval->result) < intval($result)) {
                    $result = "$eval->result";
                }
            }
            
            
            // se a consolidação não é para selecionada (statu = 10) pode continuar
            if ($result != '10') {
                return;
            } 

            $can_consolidate = true;

            /**
             * Se a consolidação requer validações, verifica se existe alguma
             * avaliação dos usuários validadores
             */
            if ($validations = $plugin->config['consolidation_requires_validations']) {
                
                foreach($validations as $slug) {
                    $can = false;
                    foreach ($evaluations as $eval) {
                        if ($eval->user->validator_for == $slug) {
                            $can = true;
                        }
                    }
                    
                    if (!$can) {
                        $can_consolidate = false;
                    }
                }
            }
            
            $has_validations = false;
            foreach ($evaluations as $eval) {
                if ($eval->user->aldirblanc_validador) {
                    $has_validations = true;
                }
            }

            // se não pode consolidar, coloca a string 'homologado'
            if (!$can_consolidate) {
                if (!$this->consolidatedResult || count($evaluations) <= 1 || !$has_validations) {
                    $result = 'homologado';
                } else if (strpos($this->consolidatedResult, 'homologado') === false) {
                    $result = "homologado, {$this->consolidatedResult}";
                } else {
                    $result = $this->consolidatedResult;
                }
            }
        });

        //Insere um conteúdo na home logo acima do formulário de pesquisa via template part ou texto setado nas configurações
        if ($this->config['featured']) {
            $app->hook($this->config['featured.hook'], function() use($plugin, $opportunity) {
                if(!$opportunity->{$plugin->prefix('featured')}) {
                    return;
                }
                $this->enqueueStyle('app', 'streamlined-opportunity', 'css/streamlinedopportunity.css');
                $this->part($plugin->config['featured.part'], ['plugin' => $plugin]);
            });
        }

        $app->hook('template(<<*>>.main-footer):begin', function () use ($plugin) {
            /** @var \MapasCulturais\Theme $this */
            if ($plugin->config['link_support_footer'] && $plugin->config['link_support']) {
                $this->part('streamlinedopportunity/support', ['linkSuporte' => $plugin->config['link_support']]);
            }
        });

        // adiciona informações do status das validações ao formulário de avaliação
        $app->hook('template(registration.view.evaluationForm.simple):before', function (Registration $registration, Opportunity $opportunity) use ($plugin) {
            $opportunities_id = $plugin->config['opportunity_id'];
            if ($opportunity->id == $opportunities_id && $registration->consolidatedResult) {
                $em = $registration->getEvaluationMethod();
                $result = $em->valueToString($registration->consolidatedResult);
                echo "<div class='alert warning'> Status das avaliações: <strong>{$result}</strong></div>";
            }
        });

        // reordena avaliações antes da reconsolidação, colocando as que tem id = registration_id no começo,
        // pois indica que foram importadas
        $app->hook('controller(opportunity).reconsolidateResult', function (Opportunity $opportunity, &$evaluations) {

            usort($evaluations, function ($a, $b) {
                if (preg_replace('#[^\d]+#', '', $a['number']) == $a['id']) {
                    return -1;
                } else if (preg_replace('#[^\d]+#', '', $b['number']) == $b['id']) {
                    return 1;
                } else {
                    $_a = (int) $a['id'];
                    $_b = (int) $b['id'];
                    return $_a <=> $_b;
                }
            });
        });

        //Seta sessão que identifica que ao criar uma nova conta, o usuário veio do plugin steamLined
        $app->hook('auth.createUser:before', function () use ($plugin, &$isStreamlined) {    
            if ($plugin->config['redirect_login_enabled'] && isset($_SESSION['mapasculturais.auth.redirect_path']) && strpos($_SESSION['mapasculturais.auth.redirect_path'], $plugin->getSlug())) {
                $_SESSION['mapasculturais.auth.FromStreamlined'] = $plugin->getSlug();
            }
        });
        
        //Se ao criar a conta, o usuário acessou pelo plugin streanlined, leva ele para o cadastro
        $app->hook('auth.successful:redirectUrl', function (&$redirectUrl) use ($plugin, $app) {
            if ($plugin->config['redirect_login_enabled'] && $_SESSION['mapasculturais.auth.FromStreamlined'] ?? null == $plugin->getSlug()) {            
                $redirectUrl = $app->createUrl($plugin->getSlug(), 'cadastro');
            }
        },1000);

        //Seta uma sessão com redirect_path do painel
        $app->hook('auth.successful', function () use ($plugin, $app) {
            $opportunities_id = $plugin->config['opportunity_id'];

            $opportunity = $app->repo('Opportunity')->find($opportunities_id) ?? null;
            
            if ($opportunity && $opportunity->canUser('@control')) {
                $_SESSION['mapasculturais.auth.redirect_path'] = $app->createUrl('panel', 'index');
            }
        });

         // Modifica o template do autenticador quando o redirect url for para um slug configurado
         $app->hook('controller(auth).render(<<*>>)', function (&$template, &$data) use ($app, $plugin) {
            $redirect_url = $_SESSION['mapasculturais.auth.redirect_path'] ?? '';
          
            if (strpos($redirect_url, "/{$plugin->getSlug()}") === 0) {
                $req = $app->request;
                $data['plugin'] = $plugin;
                $plugin->registerAssets();
                $this->layout = $plugin->config['layout'];
            }
        });

        //Altera o redirectUrl caso encontre um slug  configurado na sessão mapasculturais.auth.redirect_path
        $app->hook('auth.createUser:redirectUrl', function (&$redirectUrl) use ($plugin) {
           
            if (isset($_SESSION['mapasculturais.auth.redirect_path']) && strpos($_SESSION['mapasculturais.auth.redirect_path'], $plugin->getSlug())) {
                $redirectUrl =  $plugin->getSlug();
            }
        });

        /**
         * Na criação da inscrição, define os metadados {$slug}_registration do agente responsável pela inscrição
         * @TODO: Verificar se metadado é necessário
         */
        $app->hook('entity(Registration).insert:after', function () use ($plugin) {
            /** @var \MapasCulturais\Entities\Registration $this */
            if ($this->opportunity->id == $plugin->config['opportunity_id']) {
                $slug = $plugin->prefix("registration");
                $agent = $this->owner;
                $agent->$slug = $this->id;
                $agent->save(true);
            }
        });

        // Redireciona usuário que acessar a oportunidade dos incisos I pelo mapas para o plugin
        $app->hook('GET(opportunity.single):before', function () use ($plugin, $app) {
            $requestedOpportunity = $this->requestedEntity;

            if (!$requestedOpportunity) {
                return;
            }

            $can_view = $requestedOpportunity->canUser('@control') ||
                $requestedOpportunity->canUser('viewEvaluations') ||
                $requestedOpportunity->canUser('evaluateRegistrations');


            if (!$can_view && $requestedOpportunity->equals($plugin->opportunity) && $plugin->isEnabled()) {
                $url = $app->createUrl($plugin->getSlug(), 'cadastro');
                $app->redirect($url);
            }
        });

        // Redireciona o usuário que acessa a inscrição pelo mapas culturais para o plugin
        $app->hook('GET(registration.view):before', function () use ($plugin, $app) {
            /** @var \MapasCulturais\Controllers\Registration $this */
            $registration = $this->requestedEntity;
            $requestedOpportunity = $registration->opportunity;
            if (!$requestedOpportunity) {
                return;
            }
            $can_view = $requestedOpportunity->canUser('@control') ||
                $requestedOpportunity->canUser('viewEvaluations') ||
                $requestedOpportunity->canUser('evaluateRegistrations');

            if (!$can_view && $requestedOpportunity->equals($plugin->opportunity) && $plugin->isEnabled()) {
                $url = $app->createUrl($plugin->getSlug(), 'formulario', [$registration->id]);
                $app->redirect($url);
            }
        });

        //Publica o agente ao enviar a inscrição
        $app->hook('POST(registration.send):before', function () use ($plugin, $app) {
            $agent = $app->repo("Agent")->find($app->user->profile->id);
            $registration = $this->requestedEntity;
            if($agent->status == 0 && $registration->{$plugin->prefix("has_accepted_terms")}) {
                $agent->status = 1;
                $agent->save(true);
            }
        });
    }

    public function register()
    {
        $app = App::i();

        $app->registerController($this->getSlug(), Controllers\StreamlinedOpportunity::class);

        //Registro de metadados
        $this->registerMetadata(Registration::class, $this->prefix("has_accepted_terms"), [
            'label' => i::__('Aceite dos termos e condições'),
            'type' => 'boolean',
            'private' => true,
        ]);

        $this->registerMetadata(Opportunity::class, $this->prefix("Fields"), [
            'label' => i::__("Lista de ID dos campos " . $this->getSlug()),
            'type' => 'array',
            'serialize' => function ($val) {
                return json_encode($val);
            },
            "unserialize" => function ($val) {
                return json_decode($val);
            },
            "private" => true,
        ]);

        $this->registerAgentMetadata($this->prefix("registration"), [
            'label' => i::__('Id da inscrição no Insiso I'),
            'type' => 'string',
            'private' => true,
        ]);
        

        $this->registerMetadata('MapasCulturais\Entities\Opportunity',  $this->prefix("enabled"), [
            'label' => i::__('Aberto processo de inscrição'),
            'type' => 'boolean',
            'private' => false,
            'default_value' => true
        ]);

        $this->registerMetadata('MapasCulturais\Entities\Opportunity',  $this->prefix("featured"), [
            'label' => i::__('Destacar na home'),
            'type' => 'boolean',
            'private' => false,
            'default_value' => true
        ]);

        /**
         * Registra campo adicional "Mensagem de Recurso" nas oportunidades
         * @return void
         */
        $this->registerMetadata('MapasCulturais\Entities\Opportunity',  $this->prefix("status_appeal"), [
            'label' => i::__('Mensagem para Recurso na tela de Status'),
            'type' => 'text'
        ]);
        return;
    }

    /**
     * Retorna um json como resultado da requisição
     * 
     * @param mixed $data 
     * @param int $status 
     * @return never 
     * @throws Stop 
     */
    function json($data, $status = 200)
    {
        $app = App::i();
        $app->contentType('application/json');
        $app->halt($status, json_encode($data));
    }

    /**
     * Retorna o slug configurado para o plugin
     * @return string
     */
    public function getSlug()
    {
        return $this->config['slug'];
    }
    
    /**
     * Retorna o valor com prefixo referenciando o slug
     *
     * @param  mixed $value
     * @return string
     */
    public function prefix($value)
    {
        return $this->config['slug']."_".$value;
    }

    /**
     * Retorna o controlador registrado para a instância do plugin
     * @return Controllers\StreamlinedOpportunity
     */
    public function getController() {
        $app = App::i();
        return $app->controller($this->slug);
    }
    
    /**
     * Retorna a oportunidade configurada
     * @return Opportunity 
     */
    public function getOpportunity() {
        if(!$this->_opportunity) {
            $app = App::i();
            $this->_opportunity = $app->repo("Opportunity")->find($this->config['opportunity_id']);
        }

        return $this->_opportunity;
    }

    public function getTerms() {
        return (array) $this->config['terms'];
    }

    /**
     * Retorna a data de início das inscrições
     * @return DateTime 
     */
    public function getFromDate() {
        return $this->config['registrations.from'] ? 
            new \DateTime($this->config['registrations.from']) : 
            $this->opportunity->registrationFrom;
    }

    /**
     * Retorna a data final das inscrições
     * @return DateTime 
     */
    public function getToDate() {
        return $this->config['registrations.to'] ? 
            new \DateTime($this->config['registrations.to']) : 
            $this->opportunity->registrationTo;
    }

    /**
     * Retorna o número máximo de inscrições por usuário
     * @return void 
     */
    public function getLimit() {
        return $this->config['limit'];
    }

    /** 
     * Indica se as inscrições estão abertas 
     * @return bool
     */
    public function isRegistrationOpen() {
        $this->opportunity->isRegistrationOpen();
        
        $current_date = new \DateTime('now');
        
        return $current_date >= $this->fromDate && $current_date <= $this->toDate;
    }
    
    /**
     * Indica se a interface simplificada está ativa
     * @return bool
     */
    public function isEnabled()
    {
        $enabled = $this->opportunity->{$this->prefix('enabled')};
        return (bool) $enabled;
    }

    public function text($key) {
        return $this->config['texts'][$key];
    }
       
    
    /**
     * Retorna se a oportunidade é gerenciada pelo StreamLinedOpportunity ou não
     * @param  Opportunity $opportunity
     * @return bool
     */
    public function isStreamLinedOpportunity(Opportunity $opportunity)
    {
        if($opportunity->id == $this->config['opportunity_id']){
            return true;
        }

        return false;
    }
}
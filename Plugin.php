<?php

namespace StreamlinedOpportunity;

use Exception;
use MapasCulturais\App;
use MapasCulturais\i;
use MapasCulturais\Entities\Registration;
use MapasCulturais\Entities\Opportunity;

/**
 * @property-read String $slug slug configurado para o plugin
 * @package StreamlinedOpportunity
 */
class Plugin extends \MapasCulturais\Plugin
{

    protected static $instancesBySlug = [];
    protected static $instancesByOpportunity = [];

    public function __construct(array $config = [])
    {
        $app = App::i();

        $slug = $config['slug'] ?? null;

        if (!$slug) {
            throw new Exception(i::__('A chave de configuração "slug" é obrigatória no plugin StreamlinedOpportunity'));
        }

        
        self::$instancesBySlug[$slug] = $this;
        self::$instancesByOpportunity[$config['opportunity_id']] = $this;

        $PREFIX = strtoupper($slug);

        /*
        ENABLED_STREAM_LINED_OPPORTUNITY => {$slug}_ENABLED
        demais somente adiciona o prefixo
        */
        $config += [
            // true habilita o plugin false desabilita
            'enabled_plugin' => env("{$PREFIX}_ENABLED", false), 

            // Define o horario que deve ser liberado as inscrições
            'schedule_datetime' => null,
            'schedule_closing' => null,

            // Opportunidade configurada no StreamLinedOpportunity
            'opportunity_id' => false,

            // número máximo de inscrições por usuário
            'limit' => env("{$PREFIX}_LIMIT", 1), 

            'initial_statement_enabled' => false,

            /* TEXTOS E DEMAIS COMPONENTES DE INTERFACE */

            'layout' => "streamlined-opportunity",

            /*TEXTOS EXIBIDOS NA TELA DO INÍCIO DO CADASTRO */
            'registration_screen' => [
                'title' => env("{$PREFIX}_TITLE_REGISTRATION_SCREEN", ''),
                'description' => env("{$PREFIX}_DESCRIPTION_REGISTRATION_SCREEN", ''),
                'long_description' => env("{$PREFIX}_LONG_DESCRIPTION_REGISTRATION_SCREEN", ''),
                'title_application_summary' => env("{$PREFIX}_TITLE_APPLICATION_SUMARY", ''),
            ],

            /*TEXTOS DA TELA DO FORMULÁRIO */
            'form_screen' => [
                'title' => env("{$PREFIX}_REGISTRATION_SCREEN_TITLE", '')
            ],
            
            /*TEXTO  HOME ANTES DO FORMULARIO DE PESQUISA POR PALAVRA CHAVE*/
            'text_home_before_search' => [
                // true para usar um texto acima do formulário de pesquisa da home
                'enabled' => env("{$PREFIX}_ENABLED_TEXT_HOME_BEFORE_SEARCH", false), 

                //true para usar um template part ou false para usar diretamente texto da configuração
                'use_part' => env("{$PREFIX}_USE_PART_BEFORE_SEARCH", false), 

                'template_part' => env("{$PREFIX}_TEMPLATE_PART_BEFORE_SEARCH", 'text-home'),

                // Texto que será exibido
                'text' => "",

                'template_part' => env("{$PREFIX}_TEMPLATE_PART_BEFORE_SEARSH", 'text-home'),

                // Texto que será exibido
                'text' => "",

                 // Link que leva a documentação do edital
                 'link_documentation' => "",

                 // Texto que contem o link da documentação do edital
                 'text_link_documentation' => "",

                 // Texto informativo ao lado do link
                 'text_info_link_documentation' => "",

                //Habilita um botão abaixo do texto
                'enabled_button' => false,

                //texto dentro do botão
                'text_button' => "",

                //Link que o botão deve acessar
                'link_buton' => "",

                // Texto que será exibido no local do botão quando o mesmo esteja desabilitado
                'text_button_disabled' => "",
            ],

            /*IMAGEM  HOME ANTES DO FORMULARIO DE PESQUISA POR PALAVRA CHAVE*/
            'img_home_before_search' => [
                // true para usar uma imagem acima do texto que será inserido na home
                'enabled' => env("{$PREFIX}_ENABLED_IMG_HOME", false), 

                //true para usar um template part ou false para usar diretamente o caminho de uma imagem
                'use_part' => env("{$PREFIX}_USE_PART_IMG", false),
                // Nome do template part ou caminho da imagem que sera usada  
                'patch_or_part' => env("{$PREFIX}_PATCH_OR_PART", "img-home"), 

                // Classes css aplicadas a div da imagem
                'styles_class' => env("{$PREFIX}_STYLES_CLASS", ""),
            ],

            /*TEXTO  HOME DEPOIS DO FORMULARIO DE PESQUISA POR PALAVRA CHAVE*/
            'text_home_after_search' => [
                'text' => env("{$PREFIX}_TEXT_HOME_AFTER_SEARCH", ''),
                'button' => env("{$PREFIX}_BOTAO_HOME_AFTER_SEARCH", ''),
                'title' => env("{$PREFIX}_TITULO_HOME_AFTER_SEARCH", ''),
            ],

            // AVALIAÇÕES E RESULTADOS
            'not_display_results' => (array) json_decode(env("{$PREFIX}_NAO_EXIBIR_RESULTADOS', '[]")),
            'evaluators_user_id' => (array) json_decode(env("{$PREFIX}_AVALIADORES_DATAPREV_USER_ID", '[]')),
            'evaluators_generic_user_id' => (array) json_decode(env("{$PREFIX}_AVALIADORES_GENERICOS_USER_ID", '[]')),
            'display_result_evaluators' => (array) json_decode(env("{$PREFIX}_EXIBIR_RESULTADO_AVALIADORES", '["2", "3", "10"]')),

            // só consolida a a homologaćão se todos as validaćões já tiverem sido feitas
            'consolidation_requires_validations' => (array) json_decode(env('HOMOLOG_REQ_VALIDACOES', '[]')),

            // STATUS_SENT = 1
            'title_status_sent' => env("{$PREFIX}_STATUS_SENT_TITLE", i::__('Sua inscrição está em análise')),
            'msg_status_sent' => env("{$PREFIX}_STATUS_SENT_MESSAGE", i::__('Consulte novamente em outro momento.')),
            'text_button_status' => null,
            'text_link_button_status' => null,

            // STATUS_INVALID = 2
            'title_status_invalid' => env("{$PREFIX}_STATUS_INVALID_TITLE", i::__('Sua solicitação não foi aprovada')),
            'msg_status_invalid' => env("{$PREFIX}_STATUS_INVALID_MESSAGE", i::__('Sua inscrição foi analisada e homologada, mas invalidada após consulta em outras bases de dados oficiais.')),

            // STATUS_NOTAPPROVED = 3
            'title_status_notapproved' => env("{$PREFIX}_STATUS_NOTAPPROVED_TITLE", i::__('Sua solicitação não foi homologada')),
            'msg_status_notapproved' => env("{$PREFIX}_STATUS_NOTAPPROVED_MESSAGE", i::__('Sua inscrição foi analisada, mas não foi homologada por não atender aos requisitos de elegibilidade. </br> </br> Para realizar a retificação das informações apontadas <b>abaixo</b>, você deve enviar exclusivamente para o email suporte.mapacultural.ms@gmail.com  com as correções solicitadas até o dia 24/09/2021, data do encerramento das inscrições.')), // STATUS_NOTAPPROVED = 3

            //STATUS_WAITLIST = 8
            'title_status_waitlist' => env("{$PREFIX}_STATUS_WAITLIST_TITLE", i::__('Sua inscrição foi validada.')),
            'msg_status_waitlist' => env("{$PREFIX}_STATUS_WAITLIST_MESSAGE", i::__('Inscrição suplente.')),

            // STATUS_APPROVED = 10
            'title_status_approved' => env("{$PREFIX}_STATUS_APPROVED_TITLE", i::__('Sua solicitação foi aprovada.')),
            'msg_status_approved' => env("{$PREFIX}_STATUS_APPROVED_MESSAGE", i::__('Sua inscrição foi analisada e homologada e a solicitação do benefício validada pela FCMS. Aguardando o pagamento do benefício.')),

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
          
            /*TERMOS E CONDIÇÕES */
            "terms" => [
                "intro" => env("{$PREFIX}TERMS_INTRO", "terms-intro"),
                "title" =>  env("{$PREFIX}TERMS_TITLE", "terms-title"),
                "items" =>  env("{$PREFIX}TERMS_ITEM", '["terms-item0", "terms-item1"]'),
                "help" => env("{$PREFIX}TERMS_HELP", "terms-help"),
            ],

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
                "send_email_status" => [],
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
                        ],
                        'complement' => env("{$PREFIX}NO_SELECTED_STATUS_MESSAGE_COMPLEMENT", ""),
                        'has_appeal' => env("{$PREFIX}NO_SELECTED_STATUS_MESSAGE_HAS_APPEAL", true),
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
     * @return StreamlinedOpportunity\Plugin
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
     * @param  int $opportunity_id
     * @return StreamlinedOpportunity\Plugin
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
        $app->view->enqueueStyle('app', 'app-customization', 'streamlinedopportunity/customization.css');
        $app->view->enqueueStyle('app', 'app', 'streamlinedopportunity/app.css');
    }

    public function _init()
    {
        $app = App::i();

        $plugin = $this;
        $config = $plugin->_config;


        if (!$config['enabled_plugin']) {
            return;
        }

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


        // Envia um e-mail quando o proponenhte se inscreve 
        $app->hook('POST(registration.send):before', function() use ($plugin){
            $opportunities_id = $plugin->config['opportunity_id'];
            $registration = $this->requestedEntity;
            if($registration->opportunity->id == $opportunities_id){
                $plugin->sendEmailregistrationConfirm($registration, $plugin);
            }

        }); 

        // Dispara email quando se altera o status da inscrição
        $app->hook("entity(Registration).status(<<*>>)", function( )use ($plugin, $app){
            if($plugin->isStreamLinedOpportunity($this->opportunity)){  

                $evaluation = $app->repo("RegistrationEvaluation")->findBy(['registration' => $this]);
                
                /** @var Mapasculturais\Entities\Registration $this  */
                $plugin->sendEmalAlterStatus($this, $plugin, $evaluation);
            }
        });

        //Insere um conteúdo na home logo acima do formulário de pesquisa via template part ou texto setado nas configurações
        $app->hook('template(site.index.home-search-form):begin', function () use ($config, $plugin) {
            /** @var \MapasCulturais\Theme $this */
            $this->enqueueStyle('app', 'streamlined-opportunity', 'css/streamlinedopportunity.css');

            //Insere uma imagem acima do texto caso esteja configurada
            $img_home = $config['img_home_before_search'];
            if ($img_home['enabled']) {
                $params = [
                    'styles_class' => $img_home['styles_class'] ?: "",
                    'patch' => $img_home['patch_or_part'] ?: "",
                ];

                if ($img_home['use_part']) {
                    $this->part("streamlinedopportunity/" . $img_home['patch_or_part'], $params);
                } else {
                    $this->part("streamlinedopportunity/" . "insert-img", $params);
                }
            }

            //Insere um texto caso esteja configurado
            $text_home = $config['text_home_before_search'];
            if ($text_home['enabled']) {
                if ($text_home['use_part']) {
                    $this->part($text_home['template_part'], [
                        'enabled_button' => $text_home['enabled_button'],
                        'text_button' => $text_home['text_button'],
                        'link_button' => $text_home['link_button'],
                        'text_button_disabled' => $text_home['text_button_disabled'],
                        'text' => $text_home['text'],
                        'link_documentation' => $text_home['link_documentation'],
                        'text_link_documentation' => $text_home['text_link_documentation'],
                        'text_info_link_documentation' => $text_home['text_info_link_documentation'],
                        'isStartStreamLined' => $plugin->isStartStreamLined(),
                        'isRegistrationOpen' => (new \DateTime('now') >= new \DateTime($config['schedule_datetime']) && new \DateTime('now') < new \DateTime($config['schedule_closing'])) ? true : false,
                    ]);
                } else {
                    echo $text_home['text'];
                }
            }
        });

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
            if (isset($_SESSION['mapasculturais.auth.redirect_path']) && strpos($_SESSION['mapasculturais.auth.redirect_path'], $plugin->getSlug()) >= 0) {
                $_SESSION['mapasculturais.auth.FromStreamlined'] = $plugin->getSlug();
            }
        });
        
        //Se ao criar a conta, o usuário acessou pelo plugin streanlined, leva ele para o cadastro
        $app->hook('auth.successful:redirectUrl', function (&$redirectUrl) use ($plugin, $app) {
            if ($_SESSION['mapasculturais.auth.FromStreamlined'] ?? null == $plugin->getSlug()) {            
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
                $this->layout = $plugin->config['layout'];
            }
        });

        //Altera o redirectUrl caso encontre um slug  configurado na sessão mapasculturais.auth.redirect_path
        $app->hook('auth.createUser:redirectUrl', function (&$redirectUrl) use ($plugin) {
           
            if (isset($_SESSION['mapasculturais.auth.redirect_path']) && strpos($_SESSION['mapasculturais.auth.redirect_path'], $plugin->getSlug()) >= 0) {
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

        $app->hook('template(site.index.home-search):end', function () use ($plugin) {
            /** @var \MapasCulturais\Theme $this */
            $text = $plugin->config['text_home_after_search']['text'];
            $button = $plugin->config['text_home_after_search']['button'];
            $title = $plugin->config['text_home_after_search']['title'];

            $this->part('streamlinedopportunity/home-search', [
                'text' => $text,
                'button' => $button,
                'title' => $title,
            ]);
        });

        // Redireciona usuário que acessar a oportunidade dos incisos I pelo mapas para o plugin
        $app->hook('GET(opportunity.single):before', function () use ($plugin, $app) {
            $opportunities_id = $plugin->config['opportunity_id'];
            $requestedOpportunity = $this->requestedEntity;

            if (!$requestedOpportunity) {
                return;
            }

            $can_view = $requestedOpportunity->canUser('@control') ||
                $requestedOpportunity->canUser('viewEvaluations') ||
                $requestedOpportunity->canUser('evaluateRegistrations');


            if (!$can_view && ($requestedOpportunity->id == $opportunities_id) && $plugin->isStartStreamLined()) {
                $url = $app->createUrl($plugin->getSlug(), 'cadastro');
                $app->redirect($url);
            }
        });

        // Redireciona o usuário que acessa a inscrição pelo mapas culturais para o plugin
        $app->hook('GET(registration.view):before', function () use ($plugin, $app) {
            /** @var \MapasCulturais\Controllers\Registration $this */
            $opportunities_id = $plugin->config['opportunity_id'];
            $registration = $this->requestedEntity;
            $requestedOpportunity = $registration->opportunity;
            if (!$requestedOpportunity) {
                return;
            }
            $can_view = $requestedOpportunity->canUser('@control') ||
                $requestedOpportunity->canUser('viewEvaluations') ||
                $requestedOpportunity->canUser('evaluateRegistrations');

            if (!$can_view && ($requestedOpportunity->id == $opportunities_id) && $plugin->isStartStreamLined()) {
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

        $app->registerController($this->getSlug(), 'StreamlinedOpportunity\Controllers\StreamlinedOpportunity');

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

        $this->registerMetadata('MapasCulturais\Entities\Registration', $this->prefix("sent_emails"), [
            'label' => i::__('E-mails enviados'),
            'type' => 'json',
            'private' => true,
            'default' => '[]'
        ]);

        $this->registerMetadata('MapasCulturais\Entities\Registration', $this->prefix("last_email_status"), [
            'label' => i::__('Status do último e-mail enviado'),
            'type' => 'integer',
            'private' => true
        ]);

        $this->registerMetadata('MapasCulturais\Entities\Opportunity',  $this->prefix("streamlined_start"), [
            'label' => i::__('Aberto processo de inscrição'),
            'type' => 'boolean',
            'private' => false,
            'default' => false
        ]);

        $this->registerMetadata('MapasCulturais\Entities\Registration', $this->prefix("last_email_lot"), [
            'label' => i::__('Lotes com e-mail enviado'),
            'type' => 'json',
            'private' => true,
            'default' => '[]'
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
     * Retorna se esta aberto o processo de inscrição analisando a data e hora que esta liberado ou metadado streamlined_start
     * @TODO Refatorar para buscar data e hora de início da opportunidade
     *
     * @return void
     */
    public function isStartStreamLined()
    {
        $app = App::i();

        $config = $this->config;

        
        $open_registrations =  (new \DateTime('now') >= new \DateTime($config['schedule_datetime']) && new \DateTime('now') < new \DateTime($config['schedule_closing'])) ? true : false;
        
        if($opportunity = $app->repo("Opportunity")->find($config['opportunity_id'])){
            $metadata = $opportunity->getMetadata();        
            $streamlined_start = $metadata[$this->prefix('streamlined_start')] ?? null;            
            if($open_registrations && $streamlined_start){
                return true;
            }
        }
        

        return false;
    }
       
    /**
     * Envia email de confirmação de cadastro
     *
     */
    public function sendEmailregistrationConfirm(\MapasCulturais\Entities\Registration $registration, $plugin)
    {
        $app = App::i();

        $mustache = new \Mustache_Engine();
        $site_name = $app->view->dict('site: name', false);
        $baseUrl = $app->getBaseUrl();
        $filename = $app->view->resolveFilename("views/streamlinedopportunity", "email-regitration-confirmation.html");        
        $template = file_get_contents($filename);

        $params = [
            "baseUrl" => $baseUrl,
            "siteName" => $site_name,
            "slug" => $plugin->config['slug'],
            "projectName" => $plugin->config['email_confirm_registration']['project_name'],
            "statusTitle" =>  $plugin->config['email_confirm_registration']['status_title'],
            "urlImageBody" => $app->view->asset($plugin->config['email_confirm_registration']['url_image_body'], false),
            "registrationId" => $registration->id, 
            "userName" => $registration->owner->name,
        ];

        $content = $mustache->render($template,$params);

        $email_params = [
            'from' => $app->config['mailer.from'],
            'to' => $registration->owner->user->email,
            'subject' => $plugin->config['email_confirm_registration']['subject'],
            'body' => $content
        ];

        $app->log->debug("ENVIANDO EMAIL DE STATUS DA {$registration->number}");
        $app->createAndSendMailMessage($email_params);
    }

     /**
     * Envia email de atualização de status de uma inscrição
     *
     */
    public function sendEmalAlterStatus(\MapasCulturais\Entities\Registration $registration, $plugin, $evaluation)
    {
        $app = App::i();

        $mustache = new \Mustache_Engine();
        $site_name = $app->view->dict('site: name', false);
        $baseUrl = $app->getBaseUrl();
        $filename = $app->view->resolveFilename("views/streamlinedopportunity", "email-regitration-alter-status.html");        
        $template = file_get_contents($filename);
        $message_status = $plugin->config['email_alter_status']['message_status'][$registration->status];
        $message_appeal = $plugin->config['email_alter_status']['message_appeal'];
        $send_email_status = $plugin->config['email_alter_status']['send_email_status'];
        
        $params = [
            "baseUrl" => $baseUrl,
            "siteName" => $site_name,
            "slug" => $plugin->config['slug'],
            "projectName" => $plugin->config['email_alter_status']['project_name'],
            "statusTitle" =>  $message_status['title'],
            "statusMessage1" =>  $message_status['message']['part1'],
            "statusMessage2" =>  $message_status['message']['part2'],
            "statusMessage3" =>  $message_status['message']['part3'],
            "statusMessage4" =>  $message_status['message']['part4'],
            'evaluationTxt' => (in_array($registration->status, $send_email_status) && isset($evaluation[0]->evaluationData->obs)) ? $evaluation[0]->evaluationData->obs : null,
            "hasAppeal" => $message_status['has_appeal'],
            "messageAppealTitle" =>$message_appeal['title'],
            "messageAppealMessage" =>$message_appeal['message'],
            "urlImageBody" => $app->view->asset($plugin->config['email_alter_status']['url_image_body'], false),
            "registrationId" => $registration->id, 
            "userName" => $registration->owner->name,
            "statusNum" => $registration->status,
        ];
        $content = $mustache->render($template,$params);

        $email_params = [
            'from' => $app->config['mailer.from'],
            'to' => $registration->owner->user->email,
            'subject' => $plugin->config['email_alter_status']['subject'],
            'body' => $content,
            'bcc' => $plugin->config['email_hidden_copy']
        ];
        
        $app->log->debug("ENVIANDO EMAIL DE STATUS DA {$registration->number}");
        if($app->createAndSendMailMessage($email_params)){
            
            $sent_emails = $registration->{$this->prefix("sent_emails")};
            $sent_emails[] = [
                'type' => "alter_status",
                'timestamp' => date('Y-m-d H:i:s'),
                'loggedin_user' => [
                    'id' => $app->user->id,
                    'email' => $app->user->email,
                    'name' => $app->user->profile->name
                ],
                'email' => $email_params,
                'registration_set_status' => $registration->status
            ];

            $app->disableAccessControl();
            $registration->{$this->prefix("sent_emails")} = $sent_emails;
            $registration->save(true);
            $app->enableAccessControl();
            
        }

       
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
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

    protected static $instances = [];

    public function __construct(array $config = [])
    {
        $app = App::i();

        $slug = $config['slug'] ?? null;

        if (!$slug) {
            throw new Exception(i::__('A chave de configuração "slug" é obrigatória no plugin StreamlinedOpportunity'));
        }

        self::$instances[$slug] = $this;

        $PREFIX = strtoupper($slug);

        /*
        ENABLED_STREAM_LINED_OPPORTUNITY => {$slug}_ENABLED
        demais somente adiciona o prefixo
        */
        $config += [
            'enabled_plugin' => env("{$PREFIX}_ENABLED", false), // true habilita o plugin false desabilita
            'opportunity_id' => env("{$PREFIX}_OPPORTUNITY_ID", false),
            'limite' => env("{$PREFIX}_LIMITE", 1), // número máximo de inscrições por usuário

            /* TEXTOS E DEMAIS COMPONENTES DE INTERFACE */

            'layout' => "streamlined-opportunity",

            /*TEXTOS EXIBIDOS NA TELA DO INÍCIO DO CADASTRO */
            'registration_screen' => [
                'title' => env("{$PREFIX}_TITLE_REGISTRATION_SCREEN", ''),
                'description' => env("{$PREFIX}_DESCRIPTION_REGISTRATION_SCREEN", ''),
                'long_description' => env("{$PREFIX}_LONG_DESCRIPTION_REGISTRATION_SCREEN", ''),
            ],

            /*TEXTOS DA TELA DO FORMULÁRIO */
            'form_screen' => [
                'title' => env("{$PREFIX}_REGISTRATION_SCREEN_TITLE", '')
            ],

            'texto_home' => env("{$PREFIX}_TEXTO_HOME", ''),
            'botao_home' => env("{$PREFIX}_BOTAO_HOME", ''),
            'titulo_home' => env("{$PREFIX}_TITULO_HOME", ''),

            'text_home' => [
                'enabled' => env("{$PREFIX}_ENABLED_TEXT_HOME", false), // true para usar um texto acima do formulário de pesquisa da home
                'use_part' => env("{$PREFIX}_USE_PART", false), //true para usar um template part ou false para usar diretamente texto da configuração
                'text_or_part' => env("{$PREFIX}_TEXT_OR_PART", "") // Nome do template part ou texto que sera usado
            ],
            'img_home' => [
                'enabled' => env("{$PREFIX}_ENABLED_IMG_HOME", false), // true para usar uma imagem acima do texto que será inserido na home
                'use_part' => env("{$PREFIX}_USE_PART_IMG", false),  //true para usar um template part ou false para usar diretamente o caminho de uma imagem
                'patch_or_part' => env("{$PREFIX}_PATCH_OR_PART", "img-home"), // Nome do template part ou caminho da imagem que sera usada
                'styles_class' => env("{$PREFIX}_STYLES_CLASS", ""),
            ],

            // STATUS_SENT = 1
            'title_status_sent' => env("{$PREFIX}_STATUS_SENT_TITLE", i::__('Sua solicitação segue em análise.')),
            'msg_status_sent' => env("{$PREFIX}_STATUS_SENT_MESSAGE", i::__('Consulte novamente em outro momento. Você também receberá o resultado por e-mail.')),

            // STATUS_INVALID = 2
            'title_status_invalid' => env("{$PREFIX}_STATUS_INVALID_TITLE", i::__('Sua solicitação não foi aprovada.')),
            'msg_status_invalid' => env("{$PREFIX}_STATUS_INVALID_MESSAGE", i::__('Não atendeu aos requisitos necessários ou os recursos disponíveis foram esgotados.')),

            // STATUS_NOTAPPROVED = 3
            'title_status_notapproved' => env("{$PREFIX}_STATUS_NOTAPPROVED_TITLE", i::__('Sua solicitação foi aprovada.')),
            'msg_status_notapproved' => env("{$PREFIX}_STATUS_NOTAPPROVED_MESSAGE", i::__('Não atendeu aos requisitos necessários. Caso não concorde com o resultado você pode entrar com recurso.')), // STATUS_NOTAPPROVED = 3

            //STATUS_WAITLIST = 8
            'title_status_waitlist' => env("{$PREFIX}_STATUS_WAITLIST_TITLE", i::__('Sua solicitação foi validada.')),
            'msg_status_waitlist' => env("{$PREFIX}_STATUS_WAITLIST_MESSAGE", i::__('Inscrição suplente.')),

            // STATUS_APPROVED = 10
            'title_status_approved' => env("{$PREFIX}_STATUS_APPROVED_TITLE", i::__('Sua solicitação não foi aprovada.')),
            'msg_status_approved' => env("{$PREFIX}_STATUS_APPROVED_MESSAGE", i::__('A inscrição foi aprovada')),

            'logotipo_instituicao' => env("$PREFIX}_LOGOTIPO_INSTITUICAO", ''),
            'logotipo_central' => env("$PREFIX}_LOGOTIPO_CENTRAL", ''),
            'privacidade_termos_condicoes' => env("$PREFIX}_PRIVACIDADE_TERMOS", null),
            'link_suporte' => env("$PREFIX}_LINK_SUPORTE", null),
            'link_suporte_no_footer' => env("{$PREFIX}_LINK_SUPORTE", null),
            'msg_disabled' => env("{$PREFIX}_INCISO1_DISABLE_MESSAGE", 'Em breve!'),
            'exibir_resultado_padrao' => (array) json_decode(env("{$PREFIX}_EXIBIR_RESULTADO_PADRAO", '["1", "2", "3", "8", "10"]')),
            'msg_recurso' => env("{$PREFIX}_MENSAGEM_RECURSO", ''),
            'oportunidades_desabilitar_envio' => (array) json_decode(env("{$PREFIX}_OPORTUNIDADES_DESABILITAR_ENVIO", '[]')),
            'mensagens_envio_desabilitado' => (array) json_decode(env("{$PREFIX}_MENSAGENS_ENVIO_DESABILITADO", '[]')),
          
            /*TERMOS E CONDIÇÕES */
            "terms" => [
                "intro" => env("{$PREFIX}TERMS_INTRO", "terms-intro"),
                "title" =>  env("{$PREFIX}TERMS_TITLE", "terms-title"),
                "items" =>  env("{$PREFIX}TERMS_ITEM", '["terms-item0", "terms-item1"]'),
                "help" => env("{$PREFIX}TERMS_HELP", "terms-help"),
            ],
        ];

        parent::__construct($config);
    }

    static function getInstance(string $slug)
    {
        if (!isset(self::$instances[$slug])) {
            throw new Exception(i::__("Instância do plugin StremlinedOpportunity não encontrada: ") . $slug);
        }

        return self::$instances[$slug];
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

        //Insere um conteúdo na home logo acima do formulário de pesquisa via template part ou texto setado nas configurações
        $app->hook('template(site.index.home-search-form):begin', function () use ($config) {
            /** @var \MapasCulturais\Theme $this */
            $this->enqueueStyle('app', 'streamlined-opportunity', 'css/streamlinedopportunity.css');

            //Insere uma imagem acima do texto caso esteja configurada
            $img_home = $config['img_home'];
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
            $text_home = $config['text_home'];
            if ($text_home['enabled']) {
                if ($text_home['use_part']) {
                    $this->part($text_home['text_or_part']);
                } else {
                    echo $text_home['text_or_part'];
                }
            }
        });

        $app->hook('template(<<*>>.main-footer):begin', function () use ($plugin) {
            /** @var \MapasCulturais\Theme $this */
            if ($plugin->config['link_suporte_no_footer'] && $plugin->config['link_suporte']) {
                $this->part('streamlinedopportunity/support', ['linkSuporte' => $plugin->config['link_suporte']]);
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
            if (isset($_SESSION['mapasculturais.auth.redirect_path']) && strpos($_SESSION['mapasculturais.auth.redirect_path'], $plugin->getSlug()) === 0) {
                $redirectUrl =  $plugin->getSlug();
            }
        });

        /**
         * Na criação da inscrição, define os metadados inciso2_opportunity_id ou
         * inciso1_opportunity_id do agente responsável pela inscrição
         */
        $app->hook('entity(Registration).save:after', function () use ($plugin) {
            /** @var \MapasCulturais\Entities\Registration $this */
            if ($this->opportunity->id == $plugin->config['opportunity_id']) {
                $slug = "{$plugin->getSlug()}_registration";
                $agent = $this->owner;
                $agent->$slug = $this->id;
                $agent->save(true);
            }
        });

        $app->hook('template(site.index.home-search):end', function () use ($plugin) {
            /** @var \MapasCulturais\Theme $this */
            $texto = $plugin->config['texto_home'];
            $botao = $plugin->config['botao_home'];
            $titulo = $plugin->config['titulo_home'];

            $this->part('streamlinedopportunity/home-search', [
                'texto' => $texto,
                'botao' => $botao,
                'titulo' => $titulo,
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


            if (!$can_view && $requestedOpportunity->id == $opportunities_id) {
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

            if (!$can_view && $requestedOpportunity->id == $opportunities_id) {
                $url = $app->createUrl($plugin->getSlug(), 'formulario', [$registration->id]);
                $app->redirect($url);
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
}
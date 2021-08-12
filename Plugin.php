<?php

namespace StreamlinedOpportunity;

use Exception;
use MapasCulturais\App;
use MapasCulturais\i;

/**
 * @property-read String $slug slug configurado para o plugin
 * @package StreamlinedOpportunity
 */
class Plugin extends \MapasCulturais\Plugin
{

    public function __construct(array $config = [])
    {
        $app = App::i();

        $slug = $config['slug'] ?? null;

        if (!$slug) {
            throw new Exception(i::__('A chave de configuração "slug" é obrigatória no plugin StreamlinedOpportunity'));
        }
        $env_prefix = strtoupper($slug);

        /*
        ENABLED_STREAM_LINED_OPPORTUNITY => {$slug}_ENABLED
        demais somente adiciona o prefixo
        */
        $config += [
            'enabled_plugin' => env("{$env_prefix}_ENABLED", false),
            'texto_home'=> env("{$env_prefix}_TEXTO_HOME",''),
            'botao_home'=> env("{$env_prefix}_BOTAO_HOME",''),
            'titulo_home'=> env("{$env_prefix}_TITULO_HOME",''),
            'opportunity_id' => env("{$env_prefix}_OPPORTUNITY_ID", 199), 
            'limite' => env("{$env_prefix}_LIMITE", 1),
            'layout' => "steamlined-opportunity",
            'logotipo_instituicao' => env("$env_prefix}_LOGOTIPO_INSTITUICAO",''),
            'logotipo_central' => env("$env_prefix}_LOGOTIPO_CENTRAL",''),
            'privacidade_termos_condicoes' => env("$env_prefix}_PRIVACIDADE_TERMOS",null),
            'link_suporte' => env("$env_prefix}_LINK_SUPORTE",null),
            'text_home' => [
                'enabled' => env("{$env_prefix}_ENABLED_TEXT_HOME", false), 
                'use_part' => env("{$env_prefix}_USE_PART", false),
                'text_or_part' => env("{$env_prefix}_TEXT_OR_PART", "") 
            ],
            'img_home' => [
                'enabled' => env("{$env_prefix}_ENABLED_IMG_HOME", false), 
                'use_part' => env("{$env_prefix}_USE_PART_IMG", false),  
                'patch_or_part' => env("{$env_prefix}_PATCH_OR_PART", "img-home"),
                'styles_class' => env("{$env_prefix}_STYLES_CLASS", ""), 
            ]
        ];

        parent::__construct($config);
    }


    public function _init()
    {
        $app = App::i();

        $plugin = $this;
        $config = $plugin->_config;

      
        if(!$config['enabled_plugin']){
            return;
        }

        //Insere um conteúdo na home logo acima do formulário de pesquisa via template part ou texto setado nas configurações
        $app->hook('template(site.index.home-search-form):begin', function () use ($config) {  
            
            /** @var \MapasCulturais\Theme $this */
            $this->enqueueStyle('app', 'streamlined-opportunity', 'css/streamlinedopportunity.css');       

            //Insere uma imagem acima do texto caso esteja configurada
            $img_home = $config['img_home'];
            if($img_home['enabled']){                
                $params = [
                    'styles_class' => $img_home['styles_class'] ?: "",
                    'patch' => $img_home['patch_or_part'] ?: "",
                ];
                
                if($img_home['use_part']){
                    $this->part("streamlinedopportunity/".$img_home['patch_or_part'] , $params);
                }else{
                    $this->part("streamlinedopportunity/"."insert-img", $params);
                }
            }            
           
            //Insere um texto caso esteja configurado
            $text_home = $config['text_home'];
            if($text_home['enabled']){
                if($text_home['use_part']){
                    $this->part($text_home['text_or_part']);
                }else{
                    echo $text_home['text_or_part'];
                }
            }

        });

        // adiciona informações do status das validações ao formulário de avaliação
        $app->hook('template(registration.view.evaluationForm.simple):before', function(\MapasCulturais\Entities\Registration $registration, $opportunity) use($plugin) {
            $opportunities_id = $plugin->config['opportunity_id'];
            if ($opportunity->id == $opportunities_id && $registration->consolidatedResult) {
                $em = $registration->getEvaluationMethod();
                $result = $em->valueToString($registration->consolidatedResult);
                echo "<div class='alert warning'> Status das avaliações: <strong>{$result}</strong></div>";
            }
        });

        // reordena avaliações antes da reconsolidação, colocando as que tem id = registration_id no começo, 
        // pois indica que foram importadas
        $app->hook('controller(opportunity).reconsolidateResult', function($opportunity, &$evaluations) {

            usort($evaluations, function($a,$b) {
                if(preg_replace('#[^\d]+#', '', $a['number']) == $a['id']) {
                    return -1;
                } else if(preg_replace('#[^\d]+#', '', $b['number']) == $b['id']) {
                    return 1;
                } else {
                    $_a = (int) $a['id'];
                    $_b = (int) $b['id'];
                    return $_a <=> $_b;
                }
            });

        });


        //Seta uma sessão com redirect_path do painel 
        $app->hook('auth.successful', function() use($plugin, $app) {
            $opportunities_id = $plugin->config['opportunity_id'];

            $opportunity = $app->repo('Opportunity')->find($opportunities_id);
            
            if($opportunity->canUser('@control')) {
                $_SESSION['mapasculturais.auth.redirect_path'] = $app->createUrl('panel', 'index');
            }
        });

        // Modifica o template do autenticador quando o redirect url for para um slug configurado
        $app->hook('controller(auth).render(<<*>>)', function () use ($app, $plugin) {
            $redirect_url = $_SESSION['mapasculturais.auth.redirect_path'] ?? '';
           
            if (strpos($redirect_url, "/{$plugin->getSlug()}") === 0) {
                $req = $app->request;

                $this->layout = $plugin->config['layout'];
            }
        });

        //Altera o redirectUrl caso encontre um slug  configurado na sessão mapasculturais.auth.redirect_path
        $app->hook('auth.createUser:redirectUrl', function(&$redirectUrl) use($plugin){
            if(isset($_SESSION['mapasculturais.auth.redirect_path']) && strpos($_SESSION['mapasculturais.auth.redirect_path'], $plugin->getSlug()) === 0) {
                $redirectUrl =  $plugin->getSlug();
            } 
        });

        /**
         * Na criação da inscrição, define os metadados inciso2_opportunity_id ou 
         * inciso1_opportunity_id do agente responsável pela inscrição
         */
        $app->hook('entity(Registration).save:after', function () use ($plugin) {

            if ($this->opportunity->id == $plugin->config['opportunity_id']) {
                $slug = "{$plugin->getSlug()}_registration";
                $agent = $this->owner;
                $agent->$slug = $this->id;
                $agent->save(true);
            }
        });

        $app->hook("GET({$plugin->getSlug()}.<<*>>):before", function () use ($plugin, $app) {
            $limit = 1;

            $plugin->_config['limite'] = $limit;
        });

        $app->hook('template(site.index.home-search):end', function () use ($plugin) {
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
        $app->hook('GET(opportunity.single):before', function() use($plugin, $app) {
            $opportunities_id = $plugin->config['opportunity_id'];
            $requestedOpportunity = $this->requestedEntity;
            
            if (!$requestedOpportunity) {
                return;
            }

            $can_view = $requestedOpportunity->canUser('@control') || 
                        $requestedOpportunity->canUser('viewEvaluations') || 
                        $requestedOpportunity->canUser('evaluateRegistrations');

                        
            if(!$can_view && $requestedOpportunity->id == $opportunities_id ) {
                $url = $app->createUrl($plugin->getSlug(), 'cadastro');
                $app->redirect($url);
            }
        });

        // Redireciona o usuário que acessa a inscrição pelo mapas culturais para o plugin
        $app->hook('GET(registration.view):before', function() use($plugin, $app) {
            $opportunities_id = $plugin->config['opportunity_id'];
            $registration = $this->requestedEntity;
            $requestedOpportunity = $registration->opportunity;
            if (!$requestedOpportunity) {
                return;
            }
            $can_view = $requestedOpportunity->canUser('@control') || 
                        $requestedOpportunity->canUser('viewEvaluations') || 
                        $requestedOpportunity->canUser('evaluateRegistrations');

            if(!$can_view && $requestedOpportunity->id == $opportunities_id ) {
                $url = $app->createUrl($plugin->getSlug(), 'formulario',[$registration->id]);
                $app->redirect($url);
            }
        });

    }

    public function register ()
    {
        $app = App::i();

        $app->registerController($this->getSlug(), 'StreamlinedOpportunity\Controllers\StreamlinedOpportunity');  

        //Registro de metadados
        $this->registerMetadata('MapasCulturais\Entities\Registration', 'termos_aceitos', [
            'label' => i::__('Aceite dos termos e condições'),
            'type' => 'boolean',
            'private' => true,
        ]);

        $this->registerMetadata('MapasCulturais\Entities\Opportunity', "{$this->getSlug()}_Fields", [
            'label' => i::__("Lista de ID dos campos ".$this->getSlug()),
            'type' => 'array',
            'serialize' => function ($val) {
                return json_encode($val);
            },
            'unserialize' => function ($val) {
                return json_decode($val);
            },
            'private' => true,
        ]);

        $slug = "{$this->getSlug()}_registration";
        $this->registerAgentMetadata($slug, [
            'label' => i::__('Id da inscrição no Insiso I'),
            'type' => 'string',
            'private' => true,
        ]);
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
    public function getSlug() {
        return $this->config['slug'];
    }
}


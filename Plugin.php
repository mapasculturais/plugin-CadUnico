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

    protected static $instances = [];

    public function __construct(array $config = [])
    {
        $app = App::i();

        $slug = $config['SLUG'] ?? null;

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
            'opportunity_id' => env("{$PREFIX}_OPPORTUNITY_ID", false),

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


        ];

        parent::__construct($config);
    }

    static function getInstance(string $slug) {
        if (!isset(self::$instances[$slug])) {
            throw new Exception(i::__("Instância do plugin StremlinedOpportunity não encontrada: ") . $slug);
        }

        return self::$instances[$slug];
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
        $app->hook('template(site.index.home-search-form):begin', function () use ($plugin, $config, $app) {  
            
            $this->enqueueStyle('app', 'streamlined-opportunity', 'css/streamlinedopportunity.css');       

            //Insere uma imagem acima do texto caso esteja configurada
            $img_home = $config['img_home'];
            if($img_home['enabled']){                
                $params = [
                    'styles_class' => $img_home['styles_class'] ?: "",
                    'patch' => $img_home['patch_or_part'] ?: "",
                ];
                
                if($img_home['use_part']){
                    $this->part($img_home['patch_or_part'] , $params);
                }else{
                    $this->part("insert-img", $params);
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

  
    }

    public function register ()
    {
    }

    /**
     * Retorna o slug configurado para o plugin
     * @return string 
     */
    public function getSlug() {
        return $this->condig['slug'];
    }
}


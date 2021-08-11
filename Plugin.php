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

        $slug = $config['SLUG'] ?? null;

        if (!$slug) {
            throw new Exception(i::__('A chave de configuração "slug" é obrigatória no plugin StreamlinedOpportunity'));
        }
        $env_prefix = strtoupper($slug);

        /*
        ENABLED_STREAM_LINED_OPPORTUNITY => {$slug}_ENABLED
        demais somente adiciona o prefixo
        */
        $config += [
            'enabled_plugin' => env("{$env_prefix}_ENABLED", false), // true habilita o plugin false desabilita
            'text_home' => [
                'enabled' => env("{$env_prefix}_ENABLED_TEXT_HOME", false), // true para usar um texto acima do formulário de pesquisa da home
                'use_part' => env("{$env_prefix}_USE_PART", false), //true para usar um template part ou false para usar diretamente texto da configuração
                'text_or_part' => env("{$env_prefix}_TEXT_OR_PART", "") // Nome do template part ou texto que sera usado
            ],
            'img_home' => [
                'enabled' => env("{$env_prefix}_ENABLED_IMG_HOME", false), // true para usar uma imagem acima do texto que será inserido na home
                'use_part' => env("{$env_prefix}_USE_PART_IMG", false),  //true para usar um template part ou false para usar diretamente o caminho de uma imagem
                'patch_or_part' => env("{$env_prefix}_PATCH_OR_PART", "img-home"), // Nome do template part ou caminho da imagem que sera usada
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


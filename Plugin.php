<?php

namespace StreamlinedOpportunity;

use MapasCulturais\App;

class Plugin extends \MapasCulturais\Plugin
{

    public function __construct(array $config = [])
    {
        $app = App::i();

        $config += [
            'enabled' => env("ENABLED_STREAM_LINED_OPPORTUNITY", false),
            'text_home' => [
                'use_part' => env("USE_PART", false),
                'text_or_part' => env("TEXT_OR_PART", "")
            ]
        ];
        parent::__construct($config);
    }

    public function _init()
    {
        $app = App::i();

        $plugin = $this;
        $config = $plugin->_config;

        if(!$config['enabled']){
            return;
        }

        $app->hook('template(site.index.home-search-form):begin', function () use ($plugin, $config, $app) {            
            //Insere um texto na home logo acima do formulário de pesquisa via template part ou texto setado nas configurações
            if($config['text_home']['use_part']){
                $this->part($config['text_home']['text_or_part']);
            }else{
                echo $config['text_home']['text_or_part'];
            }
        });
    }

    public function register ()
    {
        
    }
}
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
            ],
            'img_home' => [
                'enabled' => env("ENABLED_IMG_HOME", true), // true para usar uma imagem acima do texto que será inserido na home
                'use_part' => env("USE_PART_IMG", true),  //true para usar um template part ou false para usar diretamente o caminho de uma imagem
                'patch_or_part' => env("PATCH_OR_PART", "img-home") // Nome do template part ou caminho da imagem que sera usada
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

        //Insere um conteúdo na home logo acima do formulário de pesquisa via template part ou texto setado nas configurações
        $app->hook('template(site.index.home-search-form):begin', function () use ($plugin, $config, $app) {  
            
            
            //Insere uma imagem acima do texto caso esteja configurada
            $img_home = $config['img_home'];
            if($img_home['enabled']){
                if($img_home['use_part']){
                    $this->part($img_home['patch_or_part']);
                }else{
                    $this->part("insert-img", ['patch' => $img_home['patch_or_part']]);
                }
            }            
           
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
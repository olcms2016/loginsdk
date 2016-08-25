<?php

namespace olcms\Foundation;

/**
 * 应用入口
 */
class Application extends Container
{
    
    private $config;

    //服务器提供者
    private $providers = [
        'qq' => '\olcms\Qq\Qq',
    ];
    
    public function __construct($config)
    {
        $this->config = $config;
        
        //注册服务器提供者
        $this->registerProviders();

    }
    
    /**
     * 注册服务提供者
     */
    private function registerProviders()
    { 
        foreach($this->providers as $k => $v){
            $this->s[$k] = function() use ($k, $v){
                return new $v($this->config[$k]);
            };
        }
    }
    
}

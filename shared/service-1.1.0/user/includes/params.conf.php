<?php


return array(
    'uc_server' => [
        'api_uri' => sprintf('%s://%s:%s', GlobalConfig::get('services/ucenter/prototype'), GlobalConfig::get('services/ucenter/api_host'), GlobalConfig::get('services/ucenter/api_port')),//'http://ucenter.corp-dev.dteols.cn',
        'api_version' =>  GlobalConfig::get('services/ucenter/api_version'),
        'client_id' =>  GlobalConfig::get('services/ucenter/client_id'),
        'client_secret' => GlobalConfig::get('services/ucenter/client_secret')
    ]
);

<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

return array(
    'servers' => [
        'cache' => [
            'host' => '127.0.0.1',
            'port' => '6379'
        ]
    ],
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => sprintf('mysql:host=%s;dbname=%s', \GlobalConfig::get('services/dbs/oc/server'), \GlobalConfig::get('services/dbs/oc/database')),
            'username' => \GlobalConfig::get('services/dbs/oc/account/user'),
            'password' => \GlobalConfig::get('services/dbs/oc/account/passwd'),
            'charset' =>  \GlobalConfig::get('services/dbs/oc/account/charset'),
        ],
         'db_community' => [
            'class' => 'yii\db\Connection',
            'dsn' => sprintf('mysql:host=%s;dbname=%s', \GlobalConfig::get('services/dbs/community/server'), \GlobalConfig::get('services/dbs/community/database')),
            'username' => \GlobalConfig::get('services/dbs/community/account/user'),
            'password' => \GlobalConfig::get('services/dbs/community/account/passwd'),
            'charset' =>  \GlobalConfig::get('services/dbs/community/account/charset'),
        ],
        'db_survey' => [
            'class' => 'yii\db\Connection',
            'dsn' => sprintf('mysql:host=%s;dbname=%s', \GlobalConfig::get('services/dbs/survey/server'), \GlobalConfig::get('services/dbs/survey/database')),
            'username' => \GlobalConfig::get('services/dbs/survey/account/user'),
            'password' => \GlobalConfig::get('services/dbs/survey/account/passwd'),
            'charset' =>  \GlobalConfig::get('services/dbs/survey/account/charset'),
        ]
        /*'db_resource' => [
            'class' => 'yii\db\Connection',
            'dsn' => sprintf('mysql:host=%s;dbname=%s', \GlobalConfig::get('services/dbs/resource/server'), \GlobalConfig::get('services/dbs/resource/database')),
            'username' => \GlobalConfig::get('services/dbs/resource/account/user'),
            'password' => \GlobalConfig::get('services/dbs/resource/account/passwd'),
            'charset' =>  \GlobalConfig::get('services/dbs/resource/account/charset'),
        ]*/
    ]
);

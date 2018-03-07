<?php
/**
 * Created by PhpStorm.
 * User: 吾色禅师<wuse@chanshi.me>
 * Date: 2016/10/30
 * Time: 10:43
 */

return
    [
        'cache'=>
            [
                'default' => 'file' ,
                'support' =>
                    [
                        'file' =>
                            [
                                'keyPrefix'  => 'cache_',
                                'cacheDir'   => '&runtime/cache',
                                'fileSuffix' => '.cache'
                            ],
                        'memcached' =>
                            [
                                'keyPrefix' => 'cache_',
                                'server' => '@server.memcached.server_1'
                            ]
                    ]
            ]
    ];
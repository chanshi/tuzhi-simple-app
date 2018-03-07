<?php
/**
 * Created by PhpStorm.
 * User: 吾色禅师<wuse@chanshi.me>
 * Date: 2016/10/30
 * Time: 10:44
 */

return
    [
        'server' =>
            [
                'memcached' =>
                    [
                        'server_1' =>
                            [
                                'host'=>'192.168.56.102',
                                'port'=>11211
                            ]
                    ],
                'mysql' =>
                    [
                        'master' =>
                            [
                                'driver'=>'mysql',
                                'host'=>'192.168.31.2',
                                'userName'=>'yuanfenba',
                                'password'=>'yuanfenba',
                                'schema'=>'business_yuanfenba',
                            ]
                    ]
            ]
    ];
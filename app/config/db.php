<?php
/**
 * Created by PhpStorm.
 * User: 吾色禅师<wuse@chanshi.me>
 * Date: 2016/10/30
 * Time: 10:44
 */

return
    [
        'db'=>
            [
                /**
                 * 写服务器
                 *
                 */

                'master' => '@server.mysql.master',

                /**
                 * 读服务器
                 */

//                'slave'  =>
//                    [
//                        '@server.mysql.slave'
//                    ]
            ]
    ];
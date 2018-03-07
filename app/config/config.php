<?php
/**
 * Created by PhpStorm.
 * User: 吾色禅师<wuse@chanshi.me>
 * Date: 2016/10/30
 * Time: 10:40
 */

return
    [
        'config'=>
            [
                /**
                 * 配置路径
                 */

                'path'=> __DIR__.'/',

                /**
                 * 需要加载的配置
                 */

                'loadFile'=>
                    [
                        /**
                         * 别名配置
                         */
                        'alias.php',

                        /**
                         *  APP
                         */
                        'app.php',

                        /**
                         * 命名空间
                         */
                        'namespace.php',

                        /**
                         * 视图
                         */
                        'view.php',

                        /**
                         * 缓存
                         */
                        //'cache.php',

                        /**
                         * 数据库
                         */
                        //'db.php',

                        /**
                         * 各类服务器 配置
                         */
                        //'server.php',

                        /**
                         * 权限配置
                         */
                        //'access.php',


                    ]
            ]
    ];
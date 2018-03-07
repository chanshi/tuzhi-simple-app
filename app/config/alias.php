<?php
/**
 * Created by PhpStorm.
 * User: 吾色禅师<wuse@chanshi.me>
 * Date: 2016/10/30
 * Time: 10:25
 */

return
    [
        'alias' =>
            [
                '&app'=>APP_PATH,

                //'&bui'=> BUI_PATH,
                /**
                 * 启用 Cache  Log  等需要配置
                 */
                '&runtime'=> APP_PATH.'runtime',

                /**
                 * 启用 View  需要配置
                 */
                '&view'=> APP_PATH.'resource/view',
                '&layout'=> APP_PATH.'resource/layout'
            ]
    ];
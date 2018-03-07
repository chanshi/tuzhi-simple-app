<?php
/**
 * Created by PhpStorm.
 * User: 吾色禅师<wuse@chanshi.me>
 * Date: 2016/10/30
 * Time: 10:46
 */

namespace app\control;

use Symfony\Polyfill\Mbstring\Mbstring;
/**
 * Class IndexControl
 * @package app\control
 */
class IndexControl extends \Control
{

    /**
     * @return string
     */
    public function defaultAction()
    {
        echo Mbstring::mb_strlen("abc");
        return \View::fetch('index/default');
    }
}
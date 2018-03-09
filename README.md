## composer 安装方式

```bash
$ composer create-project --prefer-dist --stability=dev  tuzhi/tuzhi-simple-app  {路径}
$ chmod -R 777 {路径}/app/runtime
```

## 如何创建控制器
```php

namespace app\control;

class IndexControl extends \Control
{

    /**
     * default 为默认行为
     */
    public function defaultAction()
    {
        // 直接返回需要渲染的视图文件
        return \View::fetch('index/default',['date'=>date("Y-M-d")]);
    }
    
    public function jsonAction()
    {
        // 或者直接返回 数组  直接输出JSON 格式
        return ['result'=>'success']
    }
}
```
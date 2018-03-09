## composer 安装方式

```bash
$ composer create-project --prefer-dist --stability=dev  tuzhi/tuzhi-simple-app  {路径}
$ chmod -R 777 {路径}/app/runtime
```

## 配置文件
> 查看具体文件 config/config.php



## 如何创建控制器
```php

namespace app\control;

class IndexControl extends \Control
{

    /**
     * 对应 url 路径 /
     */
    public function defaultAction()
    {
        // 直接返回需要渲染的视图文件
        return \View::fetch('index/default',['date'=>date("Y-M-d")]);
    }
    
    /**
     * 对应 url 路径 /index/json
     */
    public function jsonAction()
    {
        // 或者直接返回 数组  直接输出JSON 格式
        return ['result'=>'success']
    }
}
```

## 建立模型
> 本框架的模型包括 核心模型 support\model\Model 或者 \Model
> AR模型 support\database\ActionRecord 或者 \ActiveRecord 继承 核心模型
> 集合模型 support\database\Collection 或者 \Collection   继承 核心模型

### 关于核心模型的定义和应用
```php

class MyModel extends \Model
{
     protected $attFilter =
         [
             'username' ,
             'password' ,
             'verifyCode' ,
         ];
 
     protected function labels()
     {
         return
             [
                 'username' => '账户',
                 'password' => '密码',
                 'verifyCode' => '验证码'
             ];
     }
 
     /**
      * @return array
      */
     protected function rules()
     {
         return
             [
                 ['username','require'],   //校验规则 必填
                 ['password','require'], 
                 ['verifyCode','require'],
                 ['verifyCode','callback',[$this,'validVerifyCode']],  //校验规则  callback
                 ['username','callback',[$this,'validUserStatus']],
                 ['password','callback',[$this,'validAccount']]
             ];
     }
 
     /**
      * @return bool
      */
     public function validUserStatus()
     {
         $info = User::find()->where(['username'=>$this->username])->one();
 
         if(isset($info['status']) && $info['status'] != User::STATUS_NORMAL){
             return '您的账号已被禁用';
         }
         return true;
     }
 
     /**
      * @return bool
      */
     public function validVerifyCode()
     {
         if( strtolower($this->verifyCode) != strtolower(\Request::session('verifyCode')) ){
             return '验证码错误';
         }
         return true;
     }
 
     /**
      * @return bool|string
      */
     public function validAccount()
     {
 
         if( ! \User::validAccount($this->username ,$this->password) ){
             return '账户或者密码错误';
         }
         return true;
     }

}


// 调用 一般在控制器中调用
$model = new MyModel();
// 这只 模型字段 根据模型设置 只会保留 $attFilter 中保留的三个字段 其他的都会被过滤掉
$model->setAttributes( \Request::all() );

// 模型字段校验 根据 rules() 中的规则验证
if( $model->verify())
{
    //TODO:: 验证通过的操作
}else{
    //TODO:: 校验失败后的操作
    // 获取错误信息
    $model->getErrors();
    // 获取错误信息 第一条
    $model->getFirstError();
}


``` 
### 关于AR模型的定义和应用
> 根据AR模型的定义 一个文件 对应一个表
```php
class User extends \ActiveRecord
{
    
    /**
     * 我们可以定义特性
     * 关于为什么要定义特性呢 
     * 我的考虑是 查询主键不确定的集合的 增删查改 以及关联表的操作
     * AR实例 则可以对包含主键的一行进行增删查改  
     */
    use  UserTrait;
     
    public static function tableName()
    {
        // 返回表名
        return 'User';
    }
}

//定义的特性
trait UserTrait
{
    /**
     * 获取代理下线子代理ID
     *
     * @param $userId
     */
    public static function queryOfflineId( $userId )
    {
        return static::find()
            ->select(['uid','username'])
            ->where(['cuid'=>$userId])
            ->all();
    }
}

// 引用特性中的查询
User::queryOfflineId( $userId );

//直接使用AR中的方法 




```
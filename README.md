## composer 安装方式

```bash
$ composer create-project --prefer-dist --stability=dev  tuzhi/tuzhi-simple-app  {路径}
$ chmod -R 777 {路径}/app/runtime
```

## 注意 
> 代码 严格按照 PSR-4 规范标准,请在编写时注意严格按照规范编写

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

// 常用的查询方式
// 1. 根据条件查询出一个值
User::find()->where(['username'=>$this->username])->one();

// 2. 复杂条件的查询
User::find(['useranme'])
    ->whereExpression( \DB::Expression(' age > 20 '))
    ->all();
    
// 3.更新
User::modify()->where('userId'=>1)->update("userName"=>"禅师");

// 4.插入
User::getNewRecord()->insert(
  [
      "userName"=>"禅师",
      "age"=>18
  ]);

// 5.删除
User::delete()->where(['userName'=>"禅师"])->delete();

//直接使用AR中的方法 

// 1.实例化AR 的方法
$UserModel =  User::load( $value );  // $value 是主键 

// 2.显示 某个值
$UserModel["userName"]; // 显示 禅师

// 3. 修改某个值
$UserModel["userName"] = "我是禅师";
$UserModel->save();

// 4. 删除该实例
$UserModel->remove(); 

```

### 关于集合模型的定义和应用
> 主要适用于 多表的关联条件查询
```php
class UserManageList extends \Collection
{
    /**
     * @var array  查询参数
     */
    protected $attFilter =
        [
            'userId',    // 用户ID
            'userType',  // 账户类型
            'userName',
        ];

    /**
     * @return array
     */
    protected function getColumns()
    {
        return
            [
                'a.uid' ,   //用户ID
                'a.username' , // 账号
                'a.ctype' , //用户分组
                new Expression('e.username as agentName')
            ];
    }


    public function buildQuery()
    {
        $Query = $this->Query;

        $Query->select( $this->getColumns() )
            ->table(User::tableName(),'a')
            ->leftJoin(Manager::tableName(),'b','a.mid=b.uid')
            ->leftJoin(User::tableName(),'f','a.cuid=f.uid');

        $Query->leftjoin(User::tableName(),'e','a.cuid=e.uid');

        // 获取最新的分成
        $Query->leftjoin(UserDivided::tableName(),'d','a.uid=d.uid AND d.dt="'.date('Y-m-d',strtotime('-1 day')).'"');

        $this->userId
            ? $Query->where(['a.uid'=>$this->userId])
            : null;

        $this->userName
            ? $Query->andLike('a.username','%'.$this->userName.'%')
            : null;

        // 是否有区间的问题
        if( is_array($this->userType) ){
            $Query->andIn('a.ctype',$this->userType);
        }else{
            $this->userType
                ? $Query->where(['a.ctype'=>$this->userType])
                : null ;
        }

        // 设置排序
        $Query->orderBy('a.uid',Query::DESC);

    }

}

// 使用方式 以下代码在控制器中

// 设置查询条件 
$Model = new UserManageList();
$Model['userId'] = Request::get('userId','int');
$Model['userType'] = Request::get('userType','int');
$Model['userName'] = Request::get('userName');
// 设置页码
$Model->setPage( Request::get('page','int',1) ,30);
// 查询
$Model->query();

// 关于遍历集合的方式
foreach( $Model as $index=>$value )
{

}

// 关于页码信息 
/**
 *  可以定义自己的页码处理类
 *  设置保护属性 pagerClass 
 */
$Model->Pager 

```

## 关于视图
> 主要采用二步视图
> 具体的模板 参考 项目

```php
// 二步视图渲染
View::layout('index/index',
    [
        'model'=>$model
    ]
);

// 文件直接渲染
View::fetch('index/index',
    [
        'model'=>$model
    ]
);

// layout 对应的路径为 resource/layout
// 视图文件对应的路径   resource/view
// 支持简单的widget    resource/widget

// 关于 widget 查看简单的例子

name app\resource\widget;

class PagerWidget
{
    public $html;

    public $pager;

    public function __construct( $pager )
    {
        $this->pager = $pager;
        $this->createHtml();
    }

    public function createHtml()
    {
        $html = '';
        if( $this->pager ){
            $html .= '<div class="page">';
            $html .= '<span>( 每页 :'.$this->pager['pageSize'].', 总数 :'.$this->pager['count'].')</span>';
            $html .= '<a href="'.$this->pager['prev']['url'].'">上一页</a>';
            foreach($this->pager['list']  as $list){
                if( $list['selected'] ){
                    $html .= '<a class="current">'.$list['text'].'</a>';
                }else{
                    $html .= '<a href="'.$list['url'].'">'.$list['text'].'</a>';
                }
            }
            $html .= '<a href="'.$this->pager['next']['url'].'">下一页</a>';
            $html .= '</div>';
        }
        $this->html = $html;
    }


    public function __toString()
    {
        return $this->html;
    }
}

// 对应Collecter 模型中的 页码

<?php echo  new app\resource\widget\PagerWidget($Model->Pager);?>

```

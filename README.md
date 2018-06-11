# TMFac-dev
天马工场后端php框架，主要用于开发天马工场组件使用
---

## 开发规范

#### 开发者组件为独立模块，可自由遵守TP5.1规范开发
**注意事项**

>1.组件即模块，在进行组件开发时请严格遵循ThinkPHP5.1的命名规范，模块目录全部采用小写和下划线命名。(请使用从商城创建组件分支时获取的组件ID)
*组件ID为系统自动产生，命名规则是 **开发者名称首字母缩写+分支ID+组件名称首字母缩写+_+5位随机字符串**，例如 **hlhj01zb_q1d3s***
---
>2.组件后端入口地址请遵循 module/controller/method 形式 (**请尽量使用默认index控制器和index方法作为入口**)
```
// 组件模块地址示例
/hlhj/index/index
```
---
>3.在进行组件开发期间，不允许修改该组件以外的任意文件和代码（除框架根目录提供的uploads中该组件对应的资源目录）
---
>4.开发者完成组件开发后，只需将组件目录进行压缩（zip格式），压缩包名称与组件目录名称一致，压缩包结构如下：
```
组件id.zip
├─组件目录           	以组件id命名的模块目录
│  ├─
├─db			
│  ├─组件id.sql		该组件使用到的数据表sql文件			
```
---


* * * * *
## 组件目录结构
开发者进行组件开发时，请参照我们提供的模块目录结构进行开发
```
│  ├─module        		开发者组件名称
│  │  ├─controller      控制器目录 必要
│  │  ├─model           模型目录 必要
│  │  ├─view            视图目录 必要
│  │  ├─config			组件配置目录目录 非必要
│  │  ├─ ...  			其他
```

>在进行组件开发时，默认的组件目录需要需要包括 config、controller、model、view这四个目录
>****

## database配置详解：
```
    // 数据库类型
    'type'            => 'mysql',		//默认统一使用mysql，不用更改
    // 服务器地址
    'hostname'        => Env::get(SERVER_ENV.'HOSTNAME'),
    // 数据库名
    'database'        => Env::get(SERVER_ENV.'DATABASE_NAME'),
    // 用户名
    'username'        => Env::get(SERVER_ENV.'DATABASE_USERNAME'),
    // 密码
    'password'        => Env::get(SERVER_ENV.'DATABASE_PASSWORD'),
      // 数据库表前缀
    'prefix'          => '',	//由于各组件公用一个库，以组件名称作为数据库表前缀区分，所以各组件务必不要设置prefix
```
>目前我们并没有采用模块分库的形式，而是共用一个数据库，开发者只需要按如上形式配置数据库，无需改动。SERVER_ENV 是我们框架提供的一个常量，无需更改。如无特殊配置需要，模块无需单独配置database.php文件，直接使用框架提供的database.php 文件。
---

**组件开发人员在定义model时可使用以下两种方式:**

>1,在model里面指定 $table ，以ORM的形式处理model (**推荐**)
---
>2,或者直接使用Db::table('tablename')的方式**


## 资源上传目录

>框架鼓励开发者使用第三方存储，除此之外，请将所有图片等资源文件的路径务必使用框架根目录提供的uploads/模块名/..,参考如下：

```
├─appconf           	客户端需要的配置文件
├─application           应用目录
├─config                应用配置目录
├─uploads
│  ├─moduleName		组件名称（同名application中的moduleName）
│  │  ├─resources	该组件下使用到的各类图片等资源文件
```




学生网上选课系统
===============
 + 基于ThinkPHP5和AmazeUI框架开发
 + Admin可对用户信息,课程类型,专业班级信息管理维护;设置开课和选课的起止时间
 + 教师用户可以管理自己开设的课程,查看自己课程参与的学生列表并导出成excel
 + 学生用户可以查看待选课程,进行选课退选操作,可以查看自己已选的课程
 + 非选课时间学生登录后不能进行选课操作,系统会弹窗提示
 + 非开课时间教师登录后不能进行开设课程操作,系统会弹窗提示

> ThinkPHP5的运行环境要求PHP5.4以上。

## 目录结构

~~~
www  WEB部署目录（或者子目录）
├─application           应用目录
│  ├─common             公共模块目录（可以更改）
│  ├─admin              管理员模块目录
│  │  ├─controller      控制器目录
│  │  ├─model           模型目录
│  │  └─view            视图目录
│  ├─user               学生/教师用户模块目录
│  │  ├─controller      控制器目录
│  │  ├─model           模型目录
│  │  └─view            视图目录
│  │
│  ├─command.php        命令行工具配置文件
│  ├─common.php         公共函数文件
│  ├─config.php         公共配置文件
│  ├─route.php          路由配置文件
│  ├─tags.php           应用行为扩展定义文件
│  └─database.php       数据库配置文件
│
├─public                WEB目录（对外访问目录）
│
├─thinkphp              框架系统目录
│
├─extend                扩展类库目录
├─runtime               应用的运行时目录（可写，可定制）
├─vendor                第三方类库目录（Composer依赖库）
├─build.php             自动生成定义文件（参考）
├─composer.json         composer 定义文件
├─LICENSE.txt           授权说明文件
├─README.md             README 文件
├─think                 命令行入口文件
~~~
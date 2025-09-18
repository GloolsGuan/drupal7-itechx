#Daily Technical Enterprise Open Learning Service#
Core包是Dteols项目的核心业务包，负责支持全平台业务逻辑，一般位于服务器/mnt/shared/dteols_core-[version] 目录下。由Gteols项目组，研发团队维护。
有任何问题，请邮件发送至<br />
<Core.Dteols@dailyedu.com>不胜感激。

## Normal information: ##
- Project:Dteols-v1.0
- Core Version:dteols-service-v1.0
- Author: DTeolsTeam/CoreGroup <Core.Dteols@dailyedu.com>
- Team blog: [dteols.me](dteols.me)

### Dependence on: ###
- EasyYii-2.x-0.2 [https://github.com/GloolsGuan/EasyYii](https://github.com/GloolsGuan/EasyYii)
- Yii2.x [ http://www.yiiframework.com]( http://www.yiiframework.com)

### Environment: ###
- PHP5.4+
- MYSQL5.6+
- CentOS6.5+



###目前规划的交互终端包括###

1. 微信WEB端，微信API支持。
2. 移动API端。
3. 第三方API支持。
4. PC管理系统。
5. 综合运行中心。


###Core包一般规范###
基础继承 \core\base\Module (/@core/base/Module.class.php)

一般输出格式：
> [
> >>  'STATUS:success|failed|error',<br /> 
> >>  Code:2000-5000, <br />
> >>  [Returned data]<br />
> 
> ]


###Core包文件名规范###
1. 所有文件名一律使用驼峰字，首字母大写。
2. 文件名格式[类名].class.php
3. 

###如何调用###
1. 设置namespace aliase路径或者根据当前框架设置__autoload规则。
   @core 指向当前 core包路径。
2. 加载 "use \core\[Module Name]"即可。

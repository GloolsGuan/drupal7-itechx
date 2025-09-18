
## 开发计划概述 ##
#### Discovery@Glools.com 项目平台架构

>Discovery@Glools项目，是一个全球协作平台级项目，包含学习社群、业务系统、协作、实时业务同步等部分。整体架构需要多服务器同步协作运行，开发也需要分步骤实现。

+ WEB部分 | 和基于Drupal7+GloolsV1.0框架开发，以课程教学为主，兼顾社群、电商、全球合作团队端。
+ 任务服务器 | 由Python2.7 + Redis + Nginx组合开发，负责全球任务派发管理，以及实时互动。
+ 全球团队协作 | GIT服务器，由于课程包很大，编辑复杂，我们的课件系统采用GIT服务器支持协作模式。


#### Web 部分开发规划
1. 课件 | 定义课件包结构，包含【课件包：课件视频、课件信息、学习卡，开放文件包：图片等】
2. 建立Web系统结构
   - 官网：glools.com 只做官方形象品牌呈现。
   - 发现地球村项目：discovery.glools.com，只服务于学习者。
     * 支持PC端、手机移动端、PAD端、微信端。
     * 支持中、英文双语课程发布。
   - 全球合作团队：team.glools.com，只支持PC端，可以通过PAD浏览。
   - 全球课程协作：git.glools.com, 课件管理与交互服务器。


#### Web 部分技术实现
> 一套底层框架支持所有终端，实现业务管理，分离业务操作层和业务逻辑层。采用Yii2.x实现业务服务模块架构，Drupal实现业务操作逻辑层。

> 整体技术实现为了兼顾效率和框架结构应该尽可能充分使用Drupal的操作层框架部分。


###### 项目布局
+ web项目 /mnt/www/glools/[site]
  - glools.com 官方网站。
  - discovery.glools.com 课程学习。
  - team.glools.com 团队合作与业务管理。
  - git.glools.com 课件交互服务器。

+ 业务服务 Yii2.x /mnt/www/glools/service
+ 任务分发 Job Server [IP]:/mnt/www/glools/jobserver
+ 公共Library /mnt/www/glools/library

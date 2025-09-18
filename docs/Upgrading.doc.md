2019年 ｜ 等待升级优化项目文档

#### View/preset 优化为 block插件机制
问题：不应该分离block的定义和数据提供机制，虽然很好，但是难以维护。

#### glools框架（包含glools）模块提供两套程序导入机制应该合并
glools/inc_libraries/funcs.lib.inc
libraries/glools/load.lib.inc Lib_Glools_Load
这个有些复杂了。

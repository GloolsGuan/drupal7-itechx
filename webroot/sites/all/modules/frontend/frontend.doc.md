#### Questions and Answers
1. Why we need frontend module in drupal7 system?


#### Frontend 模块对Drupal7系统优化的理念基础 ####
Frontend模块是面向用户的，核心价值在于统一页面呈现逻辑，其管辖范围包含频道页（首页也是频道页）
和栏目页（包含专题页），内容详情页不在这个体系内，由Drupal7系统自行运行即可。
在Drupal7系统中，Module的规划包含“业务”、“页面布局”、“操作”、“数据支持”等多个方面。
Frontend模块的存在将接管“视图”部分，并配合Template系统和Block模块，提供更为完善的视图规划和支持。

#### 在Frontend模块中包含的新功能
- Channel, 频道支持，可以简单规划整站内容布局。
- Blocker, 动态block配置和开发支持。虽然block依然需要开发人员配合。
- Layout, Layout属于Theme体系，但是可以在frontend模块中通过"channels.setup.inc"配置。
#### Description
###### Channel
1. What is channel?
    Channel is a explorer router system for user visit your site.
    In drupal system, Path and Node is most important.


2.

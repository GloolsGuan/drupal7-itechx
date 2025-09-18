## SmartModel设计思想 ##

-- 数据资源化管理原则 --
所有数据都继承于主资源表 resource_XXX, 主资源表结构固定，附属bundle结构表自定义数据结构。

-- 数据结构种群分类原则 --
一级分类为资源分类，依据业务准则分类，为resource_XXX，resource_XXX_XXX, Resource_XXX_YYY规则分类。
主资源表为"resource_XXX"依据该主资源表也称为业务种群，可以扩展多张业务类型表，例如我们这里两个业务类型：
1. 一般内容管理,例如新闻、博文、静态页面等都属于一个内容体系，因此命名为：resource_article, 在这个种群
下包含 resource_article_news, resource_article_blog, resource_article_pages, 
resource_article_comments四种业务类型。
2. 电商相关业务类型，resource_biz, resource_biz_goods, resource_biz_activity, resource_biz_service.

-- 轻介入原则 --
SmartModel组件可根据自身业务需求决定是否启用，如果启用，只需要在业务模块的Model体系中，继承 \Gtools\SmartModel\Model类，
并根据规范声明应用即可。


-- 核心应用价值 --
1. 无需建立数据表结构。
2. 灵活业务字端配置，可随时增加字端（慎重）扩展业务结构，或者添加独立的Bundle扩展一种业务类型。
   删除字端暂时不支持，稍后会支持软删除操作。
3. 

动态数据模型，命名为“SmartModel”,可支持全类型实体数据结构，以及基于实体主数据模型的附属数据模
型体系，例如：
- product
  - product_good
  - product_lightmeal
  - product_clothing
  ...

数据模型结构通过redis保存，并自动定期跟新，依据数据模型定义文件的更新时间, xxx.smartmodel.schema。

SmartModel支持的数据存储机制包括Mysql,Redis,MongoDB等，多用于静态数据关系存储。

SmartModel的服务对象可以独立是应用模块或业务模块，但是不会贯穿或兼顾两者。
相对于产品管理，SmartModel只服务于业务模块，数据模型的定义与配置在业务模块中，而非应用模块中，属于核心业务支持的一部分。

#### 数据结构设计思想的升级 ####
在之前，我们分别独立考虑数据的存储、数据的操作、业务应用，今天作为一个数据模型整体，我们从数据应用的角度出发，而让数据的存储与管理，通过技术规范实现自动化处理。
这就是为什么SmartModel存在的意义。

问：
1. 应用模块可以自定义数据类型吗？
  可以，但是只限应用场景需求之下，与核心业务无关。
2. 应用模块与业务模块如何区分？
   应用模块位于 /applications/modules 目录下，只要业务目标是应用交互，服务于前后端系统的数据、页面访问与操作，产品设计的核心目标是满足客户操作需求。
   业务模块位于@Ebouti目录下，属于核心业务体系，与客户操作无关，主要负责规范与实现整套的业务功能与操作流程。

schema内容数据结构采用JSON格式便编写。


## 基本结构概念 ##
#### bundle ####
字段包, 数据模型定义实体。在数据模型定义中，会接触两个关键词“bundle”、“schema”,其中，bundle是在程序中应用的，表示数据模型定义的实体。“schema”是数据模型结构定义的配置文件，在各个模块中“/etc”目录下。
```
<code type="javascript/json">
var BundleSchemaSample = {
  'name' : '',
  'depend_on' : '',
  'desc' : '',
  'service_for':'Ebouti/Product/(Business Module Name or Path)',
  'update_notes':{
    '2018/07/06':{'author':'GloolsGuan<GloolsGuan@Lasooo.com>', 'note':'一句话简要说明这个版本更新目标或任务，不纠细节。'}
  },
  'fields' : {
    'field_name_a' : {

    },
    'field_name_b' : {

    }
  }
}
</code>
```

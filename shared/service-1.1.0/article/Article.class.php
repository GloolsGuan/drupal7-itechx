<?php
namespace service\article;

use service\base\Base;
use yii\web\NotFoundHttpException;

/**
 * Laughstorm
 * 文章服务类
 */
class Article extends \service\base\Module{
    
    public $articleModel = 'service\article\models\Article';//文章模型路径
    
    public function __construct($id, $parent = null, $config = []){
        parent::__construct([]);
    }


}
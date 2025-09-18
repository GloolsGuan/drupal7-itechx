<?php
namespace service\article;

use \service\base\db\ActiveRecord;

class RsArticleTag extends ActiveRecord{
	public static function tableName(){
		return 'rs_article_tag';
	}
}
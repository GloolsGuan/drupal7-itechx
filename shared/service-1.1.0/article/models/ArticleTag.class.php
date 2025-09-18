<?php

namespace service\article;

use \service\base\db\ActiveRecord;

class ArticleTag extends ActiveRecord{
	public static function tableName(){
		return 'article_tag';
	}
}
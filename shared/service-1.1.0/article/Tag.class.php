<?php


namespace service\article;

use \service\base\Module;

class Tag extends Module
{
	protected $article;

	public function init(){
		parent::init();
		$this->article = $this->module;
	}
	public function setTags($aritcle_id,array $tags = [])
	{
		
	}
}
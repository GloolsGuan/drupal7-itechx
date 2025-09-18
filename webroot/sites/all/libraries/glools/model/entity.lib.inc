<?php

Lib_Glools_Load::loadFile('glools/model', 'lib');

class Lib_Glools_Model_Entity extends Lib_Glools_Model
{
	
	protected $entity = null;
	
	protected function setEntity($entity){
		$this->entity = $entity;
	}
	
	protected function retrieveExtraData($extraName, $data){
		
	}
	
	protected function setExtraQuery($query){
		
	}
	
	protected function saveExtraData($extraName, $data){
		
	}
}
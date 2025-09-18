<?php
namespace service\xplan\meet;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Agenda extends \service\base\Module{
    
    public $meet = null;
    
    public function __construct($meet, $config=[]){
        parent::__construct(__CLASS__, $meet, $config);
    }
    
    
    public function init(){
        
    }
    
    
    /**
     * Create agenda
     * 
     * @param type $senator_id
     * @param type $meet_id
     * @param type $title
     * @param type $intro
     * @param type $expected_results
     */
    public function create($senator_id, $meet_id, $title, $intro, $expected_results){
        
    }
    
    
    /**
     * Remove agenda
     * 
     * @param type $senator_id
     * @param type $meet_id
     * @param type $agenda_id
     */
    public function remove($senator_id, $meet_id, $agenda_id){
        
    }
    
    
    /**
     * Load agenda
     * 
     */
    public function loadAgenda($meet_id, $agenda_id){
        ;
    }
    
    
    public function discuss($meet_id, $senator_id, $title='', $content, $commenter){
        
    }
    
    
    /**
     * Load discuss
     */
    public function loadDiscuss($meet_id, $comment_id){
        
    }
    
    
    /**
     * Cancel an exist agenda.
     * 
     */
    public function cancel(){
        
    }
    
    
    /**
     * Add parcitipent
     * 
     * @param type $agenda_id
     * @param type $member_id
     */
    public function addParcitipent($agenda_id, $member_id){
        
    }
    
    
    public function removeParcitipent($agenda_id, $member_id){
        
    }
    
    
    /**
     * 
     * 
     * @param type $agenda_id
     * @param string $result, approval | against | giveup | top_against
     * @param type $comment
     */
    public function vote($agenda_id, $result, $comment=''){
        
    }
    
    
    
    public function makeDecision($user_id, $result){
        
    }
    
    
}
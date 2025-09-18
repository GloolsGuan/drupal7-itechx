<?php
namespace service\college;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Course extends \service\base\Module{
    
    
    public function load($id){
        
    }
    
    
    public function createCourse(){
        
    }
    
    
    public function updateCourse(){
        
    }
    
    
    public function removeCourse($course_id, $operator_code){
        
    }
    
    
    public function retrieve($last_updated_at, $last_updated_id, $course_status){
        return ['Hello,world!', __METHOD__];
    }
    
    
    public function getCourseTags(){
        
    }
    
    
    public function buildTagsForCourse($course_id, $tags){
        
    }
    
    
    public function searchCourseByTag($tag, $course_type='community'){
        
    }
    
    
    protected function isValidSessionType($type){
        $types = $this->sessionTypes();
        return array_key_exists($type, $types);
    }
    
    
    /**
     * Course session types
     * 
     * @return type
     */
    protected function sessionTypes(){
        return array(
            'normal' => [
                'title' => '常规', 'handler' => ''
            ],
            'exam' => [
                'title' => '考试', 'handler' => ''
            ],
            'survey' => [
                'title' => '调研', 'handler' => ''
            ],
            'activity' => [
                'title' => '活动', 'handler' => ''
            ],
            'task' => [
                'title' => '任务', 'handler' => ''
            ],
            'discuss' => [
                'title' => '讨论', 'handler' => ''
            ],
            'meet' => [
                'title' => '会议', 'handler' => ''
            ]
        );
    }
    
    
    protected function sessionContentTypes(){
        return array(
            'lecture' => [
                'title' => '讲义','brief'=>'包含图片、文字与视频的一般教学文档。'
            ],
            'video' => [
                'title' => '视频', 'brief' => ''
            ],
            'html5' => [
                'title' => 'HTML5文档', 'brief' => '包含简单页面交互和动态效果的教学文档，可通过浏览器直接观看，也可以下载。'
            ],
            'ppt' => [
                'title' => 'PPT教学文档', 'brief' => '在线播放版本会转换成图片格式，会损失部分动态效果和隐藏元素。'
            ],
            'material' => [
                'title' => '课件', 'brief' => '从课件资源库提取的内容，该内容受当前session访问权限约束。'
            ]
        );
    }
}
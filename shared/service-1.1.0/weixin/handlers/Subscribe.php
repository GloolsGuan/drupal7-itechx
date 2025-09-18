<?php
namespace service\weixin\handlers;
/* 
 * Task:
 *   - Record user's weixin code to system, and build an inactive account.
 * 
 */

use Yii;
use yii\base\Component;
use \frontend\models\Account as Account;
use \frontend\components\weixin\Response as Response;
use \frontend\components\weixin\Menu as Menu;
use \frontend\components\weixin\Message as ComWeixinMessage;


class Subscribe extends \yii\base\Component
{
    
    protected $com_wx_message = null;
    
    protected $receiver = null;
    
    public function __construct(){
        
    }
    
    
    /**
     * Task:
     *   + Build user's menu
     *   + Push welcome message.
     */
    public function process($receiver){
        $this->receiver = $receiver;
        $com_wx_message = new ComWeixinMessage($receiver);
        
        //-- Build menu for current user --
        $this->buildUserMenu('normal', $receiver);
        //\GtoolsDebug::testLog(__FILE__, $receiver, __METHOD__);
        $content = $com_wx_message->build('text', ['content'=>Yii::t('com/weixin', 'Welcome!')], $receiver);
        
        return $content;
    }
    
    
    
    public function saveWeixinCode($weixin_code){
        $model_account = new Account();
        return $model_account->register([
            'weixin_code' => $weixin_code
        ]);
    }
    
    
    
    public function buildUserMenu($menu_type='normal', $receiver){
        //GtoolsDebug::testLog(__FILE__, $receiver->getAll(), __METHOD__);
        $menu = new Menu();
        
        return $menu->buildMenu('inactive_member');
    }
    
    
    /**
     * TODO: The welcome words can be load from system database.
     */
    public function welcomeWords($receiver){
        $welcome_words = '欢迎关注<a href="http://afgj.glools.com/about?user_id=%s">阿福管家</a>'.
                         '我们很高兴可以为您服务。您初次关注阿福管家可以简单<a href="http://afgj.glools.com/weixin/active?user_id=%s">激活个人账户</a>'.
                         '您也可以直接回复手机号码激活，激活您的个人账户.';
        return sprintf($welcome_words, $receiver->FromUserName, $receiver->FromUserName);
    }
    
    
    protected function recordUserLocation(){
        
    }
}
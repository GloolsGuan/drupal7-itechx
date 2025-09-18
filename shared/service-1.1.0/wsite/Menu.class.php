<?php
namespace service\wsite;

    /*
     * To change this license header, choose License Headers in Project Properties.
     * To change this template file, choose Tools | Templates
     * and open the template in the editor.
     */


/**
 * Description of Plan
 *
 * @author glools
 */

use service\wsite\models\menus;

class Menu extends \service\base\Module
{
    public function getMenus($request_app_id, $menu_group)
    {
        return menus::getMenus($request_app_id,$menu_group);
    }

    public function getPage($page_id)
    {
        return menus::getPage($page_id);
    }

    public function getWidgets($widgets_id)
    {
        return menus::getWidgets($widgets_id);
    }
}




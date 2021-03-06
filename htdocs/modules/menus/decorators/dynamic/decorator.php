<?php
/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * @copyright       The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU Public License
 * @package         Menus
 * @since           1.0
 * @author          trabis <lusopoemas@gmail.com>
 * @version         $Id$
 */

defined("XOOPS_ROOT_PATH") or die("XOOPS root path not defined");

class MenusDynamicDecorator extends MenusDecoratorAbstract implements MenusDecoratorInterface
{
    function accessFilter(&$accessFilter)
    {
    }

    function decorateMenu(&$menu)
    {
    }

    function end(&$menus)
    {
        $ret = array();
        foreach ($menus as $menu) {
            if (!preg_match('/{(MODULE\|.*)}/i', $menu['title'], $reg)) {
                $ret[] = $menu;
                continue;
            }
            $result = array_map('strtolower', explode('|', $reg[1]));
            $moduleMenus = self::_getModuleMenus($result[1], $menu['pid']);
            foreach ($moduleMenus as $mMenu) {
                $ret[] = $mMenu;
            }
        }
        $menus = $ret;
    }

    function hasAccess($menu, &$hasAccess)
    {
    }

    function start()
    {
    }

    function _getModuleMenus($dirname, $pid)
    {
        static $id = -1;
        $xoops = Xoops::getInstance();
        $helper = Menus::getInstance();
        $ret = array();

        /* @var $plugin MenusPluginInterface */
        if ($plugin = Xoops_Module_Plugin::getPlugin($dirname, 'menus')) {
            if (is_array($subMenus = $plugin->subMenus())) {
                foreach ($subMenus as $menu) {
                    $obj = $helper->getHandlerMenu()->create();
                    $obj->setVar('title', $menu['name']);
                    $obj->setVar('alt_title', $menu['name']);
                    $obj->setVar('link', $xoops->url("modules/{$dirname}/{$menu['url']}"));
                    $obj->setVar('id', $id);
                    $obj->setVar('pid', $pid);
                    $ret[] = $obj->getValues();
                    $id--;
                }
            }
        }
        return $ret;
    }
}
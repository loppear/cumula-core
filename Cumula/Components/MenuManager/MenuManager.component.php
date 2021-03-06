<?php
namespace Cumula\Components\MenuManager;

use \Cumula\Base\Component as BaseComponent;
use \Cumula\Application as Application;
use \Cumula\Render\ContentBlock as ContentBlock;

/**
 * Cumula
 *
 * Cumula — framework for the cloud.
 *
 * @package    Cumula
 * @version    0.1.0
 * @author     Seabourne Consulting
 * @license    MIT License
 * @copyright  2011 Seabourne Consulting
 * @link       http://cumula.org
 */

/**
 * MenuManager Component
 *
 * Provides an API for creating and displaying menus programmaticaly. 
 *
 * @package		Cumula
 * @subpackage	MenuManager
 * @author     Seabourne Consulting
 */
class MenuManager extends BaseComponent {
	protected $_menus;
	
	public function __construct() {
		parent::__construct();
		
		$this->_menus = array();
		A('Application')->bind('BootPreprocess', array($this, 'renderMenus'));
	}

	//Item should be an array defining an itemTitle and itemPath
	public function addMenuItem($menuId, $item) {
		if(isset($this->_menus[$menuId])) {
			$this->_menus[$menuId]['items'] = array_merge($this->_menus[$menuId]['items'], $item);
		}
	}
	
	public function addMenuItems($menuId, $items) {
		foreach($items as $item) {
			$this->addMenuItem($menuId, $item);
		}
	}
	
	public function renderMenus($event) {
		$this->dispatch('MenuCollectMenus');
		foreach($this->_menus as $menuId => $menu) {
			$this->renderBlock($this->_renderMenu($menu), $menu->menuId);
		}
	}
	
	protected function _renderMenu(Menu $menu) {
		$items = $menu->getItems();
		$output = '<ul>';
		if(count($items) > 0) {
			foreach($items as $item) {
				$output .= $this->_renderItem($item);
			}
		}
		$output .= "</ul>";
		return $output;
	}
	
	protected function _renderItem($items) {
		$output = '<li><a href="'.$this->completeUrl($items->path).'">'.$items->title.'</a>';
		if (count($items->getChildren()) > 0) {
			$output .= '<ul>';
			foreach($items->getChildren() as $item) {
				$output .= $this->_renderItem($item);
			}
			$output .= "</ul>";
		}
		$output .= '</li>';
		return $output;
	}
	
	public function newMenu($id) {
		$menu = new Menu($id);
		$this->_menus[] =& $menu;
		return $menu;
	}	
  /**
   * Implementation of the getInfo method
   * @param void
   * @return array
   **/
  public static function getInfo() {
    return array(
      'name' => 'Menu Manager Component',
      'description' => 'Cumula Component that manages menus',
      'version' => '0.1.0',
      'dependencies' => array(),
    );
  } // end function getInfo
}

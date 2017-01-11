<?php
namespace wcf\acp\page;
use wcf\page\SortablePage;

/**
 * Represents the woltlab vendor api pluginstore file list.
 * 
 * @author	Dennis Kraffczyk
 * @copyright	2011-2017 KittMedia Productions
 * @license	LGPL <http://www.gnu.org/licenses/lgpl.html>
 * @package	com.kittmedia.wcf.woltlabapi
 */
class WoltlabVendorAPIPluginstoreFileListPage extends SortablePage {
	/**
	 * @see		\wcf\page\AbstractPage::$activeMenuItem
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.content.woltlabVendorAPI.pluginstoreFileList';
	
	/**
	 * @see		\wcf\page\SortablePage::$defaultSortField
	 */
	public $defaultSortField = 'fileID';
	
	/**
	 * @see		\wcf\page\MultipleLinkPage::$objectListClassName
	 */
	public $objectListClassName = 'wcf\data\woltlab\pluginstore\file\WoltlabPluginstoreFileList';
	
	/**
	 * @see		\wcf\page\SortablePage::$validSortFields
	 */
	public $validSortFields = array('fileID', 'lastNameUpdateTime');
}

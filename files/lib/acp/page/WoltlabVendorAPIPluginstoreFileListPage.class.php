<?php
namespace wcf\acp\page;
use wcf\data\woltlab\pluginstore\file\WoltlabPluginstoreFileList;
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
	 * @inheritdoc
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.content.woltlabVendorAPI.pluginstoreFileList';
	
	/**
	 * @inheritdoc
	 */
	public $defaultSortField = 'fileID';
	
	/**
	 * @inheritdoc
	 */
	public $objectListClassName = WoltlabPluginstoreFileList::class;
	
	/**
	 * @inheritdoc
	 */
	public $validSortFields = ['fileID', 'lastNameUpdateTime'];
}

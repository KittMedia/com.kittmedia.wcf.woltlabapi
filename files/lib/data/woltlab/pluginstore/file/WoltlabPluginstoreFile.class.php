<?php
namespace wcf\data\woltlab\pluginstore\file;
use wcf\data\DatabaseObject;
use wcf\data\ITitledObject;
use wcf\system\WCF;

/**
 * Represents a woltlab pluginstore file in the database.
 * 
 * @author	Dennis Kraffczyk
 * @copyright	2011-2017 KittMedia Productions
 * @license	LGPL <http://www.gnu.org/licenses/lgpl.html>
 * @package	com.kittmedia.wcf.woltlabapi
 */
class WoltlabPluginstoreFile extends DatabaseObject implements ITitledObject {
	/**
	 * @see		wcf\data\DatabaseObject:$databaseTableName
	 */
	protected static $databaseTableName = 'woltlab_pluginstore_file';
	
	/**
	 * @see		wcf\data\DatabaseObject:$databaseTableIndexName
	 */
	protected static $databaseTableIndexName = 'fileID';
	
	/**
	 * @see		wcf\data\ITitledObject::getTitle()
	 */
	public function getTitle() {
		return WCF::getLanguage()->get($this->name);
	}
	
	/**
	 * Returns the link to the WoltLab Plugin–Store.
	 * @return	string
	 */
	public function getPluginstoreLink() {
		return str_ireplace(
			array('{$fileID}', '?l={$languageID}'),
			array($this->getObjectID(), ''),
			WOLTLAB_PLUGIN_STORE_URL_SCHEME
		);
	}
}

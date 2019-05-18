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
 * @property-read	integer		$fileID			unique id of the woltlab pluginstore file
 * @property-read	boolean		$isDisabled		`1` if the woltlab pluginstore file is disabled, otherwise `0`
 * @property-read	integer|null	$lastNameUpdateTime	unix timestamp of the time where the name has been automatically updated or `null` if information is not available
 * @property-read	string		$name			name of the file (usually a language variable)
 */
class WoltlabPluginstoreFile extends DatabaseObject implements ITitledObject {
	/**
	 * @inheritdoc
	 */
	protected static $databaseTableName = 'woltlab_pluginstore_file';
	
	/**
	 * @inheritdoc
	 */
	protected static $databaseTableIndexName = 'fileID';
	
	/**
	 * @inheritdoc
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
			['{$fileID}', '?l={$languageID}'],
			[$this->getObjectID(), ''],
			WOLTLAB_PLUGIN_STORE_URL_SCHEME
		);
	}
}

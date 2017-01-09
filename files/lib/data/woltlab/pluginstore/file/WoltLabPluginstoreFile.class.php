<?php
namespace wcf\data\woltlab\pluginstore\file;
use wcf\data\DatabaseObject;

/**
 * Represents a woltlab pluginstore file in the database.
 * 
 * @author	Dennis Kraffczyk
 * @copyright	2011-2017 KittMedia Productions
 * @license	LGPL <http://www.gnu.org/licenses/lgpl.html>
 * @package	com.kittmedia.wcf.woltlabapi
 */
class WoltLabPluginstoreFile extends DatabaseObject {
	/**
	 * @see		wcf\data\DatabaseObject:$databaseTableName
	 */
	protected static $databaseTableName = 'woltlab_pluginstore_file';
	
	/**
	 * @see		wcf\data\DatabaseObject:$databaseTableIndexName
	 */
	protected static $databaseTableIndexName = 'fileID';
}

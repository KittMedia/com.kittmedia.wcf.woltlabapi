<?php
namespace wcf\data\woltlab\pluginstore\file;
use wcf\data\DatabaseObjectEditor;

/**
 * Provides functions to edit woltlab pluginstore files.
 * 
 * @author	Dennis Kraffczyk
 * @copyright	2011-2017 KittMedia Productions
 * @license	LGPL <http://www.gnu.org/licenses/lgpl.html>
 * @package	com.kittmedia.wcf.woltlabapi
 * @method	WoltlabPluginstoreFile		getDecoratedObject()
 */
class WoltlabPluginstoreFileEditor extends DatabaseObjectEditor {
	/**
	 * @inheritdoc
	 */
	protected static $baseClass = WoltlabPluginstoreFile::class;
}

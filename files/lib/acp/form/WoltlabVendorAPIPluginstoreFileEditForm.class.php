<?php
namespace wcf\acp\form;
use wcf\data\package\PackageCache;
use wcf\data\woltlab\pluginstore\file\WoltlabPluginstoreFile;
use wcf\data\woltlab\pluginstore\file\WoltlabPluginstoreFileAction;
use wcf\form\AbstractForm;
use wcf\system\language\I18nHandler;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\UserInputException;
use wcf\system\WCF;

/**
 * Represents the woltlab vendor api pluginstore file edit form.
 * 
 * @author	Dennis Kraffczyk
 * @copyright	2011-2017 KittMedia Productions
 * @license	LGPL <http://www.gnu.org/licenses/lgpl.html>
 * @package	com.kittmedia.wcf.woltlabapi
 */
class WoltlabVendorAPIPluginstoreFileEditForm extends AbstractForm {
	/**
	 * @inheritdoc
	 */
	public $action = 'edit';
	
	/**
	 * @inheritdoc
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.content.woltlabVendorAPI.pluginstoreFileList';
	
	/**
	 * @inheritdoc
	 */
	public $templateName = 'woltlabVendorAPIPluginstoreFileEdit';
	
	/**
	 * File
	 * @var		WoltlabPluginstoreFile
	 */
	public $file = null;
	
	/**
	 * File id
	 * @var		integer
	 */
	public $fileID = 0;
	
	/**
	 * Indicates if this file is disabled
	 * @var		boolean
	 */
	public $isDisabled = false;
	
	/**
	 * @inheritdoc
	 */
	public function readParameters() {
		parent::readParameters();
		
		if (isset($_REQUEST['id'])) $this->fileID = intval($_REQUEST['id']);
		
		$this->file = new WoltlabPluginstoreFile($this->fileID);
		if (!$this->file->getObjectID()) {
			throw new IllegalLinkException();
		}
	}
	
	/**
	 * @inheritdoc
	 */
	public function readData() {
		I18nHandler::getInstance()->register('name');
		parent::readData();
		
		if (empty($_POST)) {
			I18nHandler::getInstance()->setOptions(
				'name', 
				PackageCache::getInstance()->getPackageID('com.kittmedia.wcf.woltlabapi'),
				$this->file->name,
				'wcf.woltlabapi.file\[0-9]+'
			);
			
			$this->isDisabled = $this->file->isDisabled;
		}
	}
	
	/**
	 * @inheritdoc
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		I18nHandler::getInstance()->assignVariables(!empty($_POST));
		
		WCF::getTPL()->assign([
			'file' => $this->file,
			'fileID' => $this->file->getObjectID(),
			'isDisabled' => $this->isDisabled
		]);
	}
	
	/**
	 * @inheritdoc
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		I18nHandler::getInstance()->readValues();
		if (isset($_POST['isDisabled'])) $this->isDisabled = true;
	}
	
	/**
	 * @inheritdoc
	 */
	public function validate() {
		parent::validate();
		
		if (!I18nHandler::getInstance()->validateValue('name')) {
			if (I18nHandler::getInstance()->isPlainValue('name')) {
				throw new UserInputException('name');
			}
			else {
				throw new UserInputException('name', 'multilingual');
			}
		}
	}
	
	/**
	 * @inheritdoc
	 */
	public function save() {
		parent::save();
		
		$name = 'wcf.woltlabapi.file'.$this->file->getObjectID();
		if (I18nHandler::getInstance()->isPlainValue('name')) {
			I18nHandler::getInstance()->remove($name);
			$name = I18nHandler::getInstance()->getValue('name');
		}
		else {
			I18nHandler::getInstance()->save(
				'name',
				$name,
				'wcf.woltlabapi',
				PackageCache::getInstance()->getPackageID('com.kittmedia.wcf.woltlabapi')
			);
		}
		
		$this->objectAction = new WoltlabPluginstoreFileAction([$this->file], 'update', [
			'data' => [
				'name' => $name,
				'isDisabled' => (int) $this->isDisabled,
				'lastNameUpdateTime' => TIME_NOW
			]]
		);
		$this->objectAction->executeAction();
		
		$this->saved();
		I18nHandler::getInstance()->reset();
		
		WCF::getTPL()->assign('success', true);
	}
}

<?php
namespace wcf\data\woltlab\pluginstore\file;
use wcf\data\package\PackageCache;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\IToggleAction;
use wcf\system\language\I18nHandler;
use wcf\system\language\LanguageFactory;
use wcf\system\WCF;

/**
 * Provides actions for woltlab pluginstore files.
 * 
 * @author	Dennis Kraffczyk, Matthias Kittsteiner
 * @copyright	2021 KittMedia
 * @license	LGPL <http://www.gnu.org/licenses/lgpl.html>
 * @package	com.kittmedia.wcf.woltlabapi
 * @method	WoltlabPluginstoreFileEditor[]		getObjects()
 * @method	WoltlabPluginstoreFileEditor		getSingleObject()
 * @property	WoltLabPluginStoreFileEditor[]		$objects
 */
class WoltlabPluginstoreFileAction extends AbstractDatabaseObjectAction implements IToggleAction {
	/**
	 * @inheritdoc
	 */
	protected $className = WoltlabPluginstoreFileEditor::class;
	
	/**
	 * @inheritdoc
	 */
	public function create() {
		/** @var	WoltlabPluginstoreFile		 $file */
		$file = parent::create();
		
		$fileAction = new WoltlabPluginstoreFileAction([$file], 'updateTitle', $this->parameters);
		$fileAction->executeAction();
		
		// reload
		$file = new WoltlabPluginstoreFile($file->getObjectID());
		
		return $file;
	}
	
	/**
	 * @inheritdoc
	 */
	public function delete() {
		// delete language variables
		foreach ($this->getObjectIDs() as $fileID) {
			I18nHandler::getInstance()->remove('wcf.woltlabapi.file'.$fileID);
		}
		
		return parent::delete();
	}
	
	/**
	 * @inheritdoc
	 */
	public function toggle() {
		if (empty($this->objects)) {
			$this->readObjects();
		}
		
		foreach ($this->getObjects() as $fileEditor) {
			$fileEditor->update([
				'isDisabled' => ($fileEditor->getDecoratedObject()->isDisabled ? 0 : 1)
			]);
		}
	}
	
	/**
	 * Update the title of a plugin store file.
	 */
	public function updateTitle() {
		if (empty($this->objects)) {
			$this->readObjects();
		}
		
		foreach ($this->getObjects() as $fileEditor) {
			foreach (LanguageFactory::getInstance()->getLanguages() as $language) {
				$languageCode = mb_strtolower($language->getFixedLanguageCode());
				$title = $this->parameters['file']['name'][$languageCode];
				
				// manipulate $_POST as it is not possible
				// to use the I18nHandler in another way
				$_POST['pluginstoreFileTitle_i18n'][$language->getObjectID()] = $title;
			}
			
			// save i18n values
			$languageVariableName = 'wcf.woltlabapi.file' . $fileEditor->getDecoratedObject()->getObjectID();
			I18nHandler::getInstance()->register('pluginstoreFileTitle');
			I18nHandler::getInstance()->readValues();
			I18nHandler::getInstance()->save(
				'pluginstoreFileTitle',
				$languageVariableName,
				'wcf.woltlabapi',
				PackageCache::getInstance()->getPackageID('com.kittmedia.wcf.woltlabapi')
			);
			I18nHandler::getInstance()->reset();
			
			// update name if needed
			if (($languageVariableName !== $fileEditor->getDecoratedObject()->name && WCF::getLanguage()->get($languageVariableName) !== $languageVariableName) || empty($fileEditor->getDecoratedObject()->lastNameUpdateTime)) {
				$fileEditor->update([
					'lastNameUpdateTime' => TIME_NOW,
					'name' => $languageVariableName
				]);
			}
			else {
				// update for correct calculation of last check
				$fileEditor->update([
					'lastNameUpdateTime' => TIME_NOW
				]);
			}
		}
	}
	
	/**
	 * @inheritdoc
	 */
	public function validateToggle() {}
	
	/**
	 * Validates the 'updateTitle()'–action.
	 */
	public function validateUpdateTitle() {
		parent::validateUpdate();
	}
}

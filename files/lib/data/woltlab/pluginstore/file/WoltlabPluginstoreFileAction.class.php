<?php
namespace wcf\data\woltlab\pluginstore\file;
use wcf\data\package\PackageCache;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\IToggleAction;
use wcf\system\language\I18nHandler;
use wcf\system\language\LanguageFactory;
use wcf\system\WCF;
use wcf\util\HTTPRequest;

/**
 * Provides actions for woltlab pluginstore files.
 * 
 * @author	Dennis Kraffczyk
 * @copyright	2011-2017 KittMedia Productions
 * @license	LGPL <http://www.gnu.org/licenses/lgpl.html>
 * @package	com.kittmedia.wcf.woltlabapi
 * @method	WoltlabPluginstoreFileEditor[]		getObjects()
 * @method	WoltlabPluginstoreFileEditor		getSingleObject()
 */
class WoltlabPluginstoreFileAction extends AbstractDatabaseObjectAction implements IToggleAction {
	/**
	 * @inheritdoc
	 */
	protected $className = 'wcf\data\woltlab\pluginstore\file\WoltlabPluginstoreFileEditor';
	
	/**
	 * @inheritdoc
	 */
	public function create() {
		$file = parent::create();
		
		if (isset($this->parameters['fetchLocalizedTitle']) && $this->parameters['fetchLocalizedTitle']) {
			$fileAction = new WoltlabPluginstoreFileAction([$file], 'fetchLocalizedTitle');
			$fileAction->executeAction();
			
			// reload
			$file = new WoltlabPluginstoreFile($file->getObjectID());
		}
		
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
		
		foreach ($this->objects as $fileEditor) {
			$fileEditor->update([
				'isDisabled' => ($fileEditor->getDecoratedObject()->isDisabled ? 0 : 1)
			]);
		}
	}
	
	/**
	 * @inheritdoc
	 */
	public function validateToggle() {}
	
	/**
	 * Validates the 'fetchLocalizedTitle()'–action.
	 */
	public function validateFetchLocalizedTitle() {
		parent::validateUpdate();
	}
	
	/**
	 * Fetches the localized title from the file entry pages
	 * of the WoltLab Plugin–Store and saves it via the I18nHandler.
	 */
	public function fetchLocalizedTitle() {
		if (empty($this->objects)) {
			$this->readObjects();
		}
		
		foreach ($this->objects as $fileEditor) {
			foreach (LanguageFactory::getInstance()->getLanguages() as $language) {
				$languageCode = mb_strtoupper($language->getFixedLanguageCode());
				$replacementMap = [
					0 => $fileEditor->getDecoratedObject()->getObjectID(),
					1 => WOLTLAB_INTERFACE_LANGUAGE_ID_EN
				];
				
				if (defined('WOLTLAB_INTERFACE_LANGUAGE_ID_'.$languageCode)) {
					$replacementMap[1] = constant('WOLTLAB_INTERFACE_LANGUAGE_ID_'.$languageCode);
				}
				
				$httpRequest = new HTTPRequest(
					str_replace(
						['{$fileID}', '{$languageID}'],
						$replacementMap,
						WOLTLAB_PLUGIN_STORE_URL_SCHEME
					)
				);
				$httpRequest->execute();
				$replyData = $httpRequest->getReply();
				
				// fetch content of title element
				libxml_use_internal_errors(true);
				$document = new \DOMDocument();
				$document->loadHTML($replyData['body']);
				$titleElements = $document->getElementsByTagName('title');
				$title = mb_substr($titleElements->item(0)->nodeValue, 0, - 11);
				libxml_use_internal_errors(false);
				
				unset($httpRequest);
				
				// manipulate $_POST as it is not possible
				// to use the I18nHandler in another way
				$_POST['pluginstoreFileTitle_i18n'][$language->getObjectID()] = utf8_decode($title);
			}
			
			// save i18n values
			$languageVariableName = 'wcf.woltlabapi.file'.$fileEditor->getDecoratedObject()->getObjectID();
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
}

<?php
namespace wcf\system\cronjob;
use wcf\data\cronjob\Cronjob;
use wcf\data\woltlab\pluginstore\file\WoltlabPluginstoreFileAction;
use wcf\data\woltlab\pluginstore\file\WoltlabPluginstoreFileList;
use wcf\system\cronjob\AbstractCronjob;
use wcf\system\api\woltlab\vendor\WoltlabVendorAPI;
use wcf\system\exception\HTTPServerErrorException;

/**
 * Saves the over the WoltLab Vendor API delivered own products
 * for local usage in the database.
 * The localized title from the WoltLab Plugin–Store file entry pages
 * will be automatically downloaded, too.
 * 
 * @author	Dennis Kraffczyk
 * @copyright	2011-2017 KittMedia Productions
 * @license	LGPL <http://www.gnu.org/licenses/lgpl.html>
 * @package	com.kittmedia.wcf.woltlabapi
 */
class WoltlabVendorAPIPluginStoreFileIDsDownloadCronjob extends AbstractCronjob {
	/**
	 * List of current files
	 * @var		wcf\data\woltlab\pluginstore\file\WoltlabPluginstoreFileList
	 */
	public $fileList = null;
	
	/**
	 * @inheritdoc
	 */
	public function execute(Cronjob $cronjob) {
		parent::execute($cronjob);
		
		if (empty(WOLTLAB_ID) || empty(WOLTLAB_API_KEY)) {
			return;
		}
		
		// read current files from database
		$this->fileList = new WoltlabPluginstoreFileList();
		$this->fileList->readObjects();
		
		// get own file ids from api
		$ownFileIDs = WoltlabVendorAPI::getInstance()->getOwnPluginStoreFileIDs();
		
		// get new file ids
		$newFileIDs = array_diff(
			$ownFileIDs,
			empty($this->fileList->getObjectIDs()) ? [] : $this->fileList->getObjectIDs()
		);
		
		if (!empty($newFileIDs)) {
			$fetchLocalizedTitles = true;
			foreach ($newFileIDs as $fileID) {
				try {
					$fileAction = new WoltlabPluginstoreFileAction([], 'create', [
						'data' => [
							'fileID' => $fileID,
							'name' => 'wcf.woltlabapi.file'.$fileID
						],
						'fetchLocalizedTitle' => $fetchLocalizedTitles
					]);
					$fileAction->executeAction();
				}
				catch (HTTPServerErrorException $e) {
					// ignore it as usually its cloudflare
					// that is blocking us
					// getting into the blocking state of cloudflare
					// usually only happens during the first import (=download)
					// of the localized titles from the WoltLab Plugin–Store over two
					// HTTPS requests per file
					// 
					// as at this point the file is already stored in the database
					// no information will be lost
					// 
					// we disable the import of the localizations at this point
					// missing localization will be imported at next execution of
					// this cronjob
					$fetchLocalizedTitles = false;
				}
			}
		}
		else {
			$filesWithoutLocalization = [];
			$filesWithLocalizationThatShouldBeChecked = [];
			
			foreach ($ownFileIDs as $fileID) {
				if (($file = $this->fileList->search($fileID))) {
					if (empty($file->lastNameUpdateTime)) {
						$filesWithoutLocalization[] = $file;
					}
					else if ($file->lastNameUpdateTime <= (TIME_NOW - 259200)) {
						$filesWithLocalizationThatShouldBeChecked[] = $file;
					}
				}
			}
			
			try {
				// files without localization have priority
				if (!empty($filesWithoutLocalization)) {
					$fileAction = new WoltlabPluginstoreFileAction($filesWithoutLocalization, 'fetchLocalizedTitle');
					$fileAction->executeAction();
				}
				else if (!empty($filesWithLocalizationThatShouldBeChecked)) {
					$fileAction = new WoltlabPluginstoreFileAction($filesWithLocalizationThatShouldBeChecked, 'fetchLocalizedTitle');
					$fileAction->executeAction();
				}
			}
			catch (HTTPServerErrorException $e) {
				// throw error because after 24 hours (usually the time between executions)
				// cloudflare should not blocking us anymore
				throw $e;
			}
		}
	}
}

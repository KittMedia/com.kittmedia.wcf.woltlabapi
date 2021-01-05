<?php
namespace wcf\system\cronjob;
use wcf\data\cronjob\Cronjob;
use wcf\data\woltlab\pluginstore\file\WoltlabPluginstoreFileAction;
use wcf\data\woltlab\pluginstore\file\WoltlabPluginstoreFileList;
use wcf\system\api\woltlab\vendor\WoltlabVendorAPI;

/**
 * Saves the over the WoltLab Vendor API delivered own products
 * for local usage in the database.
 * The localized title from the WoltLab Plugin–Store file entry pages
 * will be automatically downloaded, too.
 * 
 * @author	Dennis Kraffczyk, Matthias Kittsteiner
 * @copyright	2021 KittMedia
 * @license	LGPL <http://www.gnu.org/licenses/lgpl.html>
 * @package	com.kittmedia.wcf.woltlabapi
 */
class WoltlabVendorAPIPluginStoreFileIDsDownloadCronjob extends AbstractCronjob {
	/**
	 * List of current files
	 * @var		WoltlabPluginstoreFileList
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
		
		// get own files from api
		$ownFiles = WoltlabVendorAPI::getInstance()->getPluginStoreFilesByUser();
		// current file list
		$currentFileList = $this->fileList->getObjectIDs();
		
		if (empty($ownFiles)) {
			return;
		}
		
		foreach ($ownFiles as $file) {
			if (!in_array($file['fileID'], $currentFileList)) {
				$fileAction = new WoltlabPluginstoreFileAction([], 'create', [
					'data' => [
						'fileID' => $file['fileID'],
						'name' => 'wcf.woltlabapi.file'.$file['fileID']
					],
					'file' => $file
				]);
				$fileAction->executeAction();
			}
			else {
				$needsTitleUpdate = false;
				
				if ($fileListFile = $this->fileList->search($file['fileID'])) {
					if (empty($file->lastNameUpdateTime)) {
						$needsTitleUpdate = true;
					}
					else if ($file->lastNameUpdateTime <= (TIME_NOW - 259200)) {
						$needsTitleUpdate = true;
					}
				}
				
				if ($needsTitleUpdate) {
					$fileAction = new WoltlabPluginstoreFileAction([$file['fileID']], 'updateTitle', [
						'file' => $file,
					]);
					$fileAction->executeAction();
				}
			}
		}
	}
}

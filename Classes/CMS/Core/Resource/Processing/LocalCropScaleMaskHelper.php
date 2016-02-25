<?php
namespace Scarbous\MrTinypng\CMS\Core\Resource\Processing;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use \TYPO3\CMS\Core\Resource\Processing\TaskInterface,
	\TYPO3\CMS\Core\Resource,
	TYPO3\CMS\Core\Utility\GeneralUtility,
	Scarbous\MrTinypng\Service\TinypngService;

/**
 * Extends the LocalCropScaleMaskHelper and add the Tinypng process
 *
 * @package Scarbous\MrTinypng\CMS\Core\Resource\Processing
 * @author Sascha Heilmeier <s.heilmeier@misterknister.com>
 */
class LocalCropScaleMaskHelper extends \TYPO3\CMS\Core\Resource\Processing\LocalCropScaleMaskHelper
{
	/**
	 * The temp-directory
	 *
	 * @var string
	 */
	public $tempPath = 'typo3temp/pics/';

	/**
	 * Process File
	 *
	 * @param TaskInterface $task The Task interface
	 *
	 * @return array|NULL
	 */
	public function process(TaskInterface $task)
	{
		$sourceFile = $task->getSourceFile();
		$result = parent::process($task);


		$objectManager = GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);
		$tinypngService = $objectManager->get(TinypngService::class);

		if ($result !== NULL) {
			if (file_exists($result['filePath'])) {

				$tinypngService->shrinkImage($result['filePath']);

				return $result;
			}
		} else {
			$originalFileName = $sourceFile->getForLocalProcessing(false);
			$originalPathInfo = pathinfo($originalFileName);
			$theOutputName = GeneralUtility::shortMD5($originalFileName . filemtime($originalFileName));

			// Making the temporary filename:
			$tmpPath = GeneralUtility::isFirstPartOfStr($this->tempPath,
				PATH_site) ? $tmpPath = $this->tempPath : PATH_site . $this->tempPath;

			// Making the temporary filename:
			if (!@is_dir($tmpPath)) {
				GeneralUtility::mkdir($tmpPath);
			}

			$toFile = $this->tempPath . $theOutputName . '.' . $originalPathInfo['extension'];

			$tinypngService->shrinkImage($originalFileName, $toFile);

			$imageSize = getimagesize($toFile);

			$result = [
				'width'    => $imageSize[0],
				'height'   => $imageSize[1],
				'filePath' => $toFile
			];

			return $result;
		}

		return NULL;
	}
}
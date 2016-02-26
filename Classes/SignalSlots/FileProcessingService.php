<?php
namespace Scarbous\MrTinypng\SignalSlots;

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

use TYPO3\CMS\Core\Utility\GeneralUtility,
	TYPO3\CMS\Core\Resource,
	TYPO3\CMS\Core\Resource\ProcessedFileRepository,
	Scarbous\MrTinypng\Service\TinypngService;

/**
 * This Class does the compression
 *
 * @package Scarbous\MrTinypng\SignalSlots
 * @author Sascha Heilmeier <s.heilmeier@misterknister.com>
 */
class FileProcessingService
{
	/**
	 * The TinypngService
	 *
	 * @var \Scarbous\MrTinypng\Service\TinypngService
	 * @inject
	 */
	protected $tinypngService;

	/**
	 * The ProcessedFileRepository
	 *
	 * @var \TYPO3\CMS\Core\Resource\ProcessedFileRepository
	 * @inject
	 */
	protected $processedFileRepository;

	/**
	 * Should it shrink
	 *
	 * @var bool
	 */
	static protected $shrink = false;

	/**
	 * Inject the TinypngService
	 * @param TinypngService $tinypngService
	 *
	 * @return void
	 */
	function injectTinypngService(TinypngService $tinypngService)
	{
		$this->tinypngService = $tinypngService;
	}

	/**
	 * Inject the ProcessedFileRepository
	 * @param \TYPO3\CMS\Core\Resource\ProcessedFileRepository $processedFileRepository
	 *
	 * @return void
	 */
	public function injectProcessedFileRepository(ProcessedFileRepository $processedFileRepository)
	{
		$this->processedFileRepository = $processedFileRepository;
	}

	/**
	 * Pre
	 *
	 * @param Resource\Service\FileProcessingService $fileProcessingService
	 * @param Resource\Driver\DriverInterface $driver
	 * @param Resource\ProcessedFile $processedFile
	 * @param Resource\FileInterface $file
	 * @param string $context
	 * @param array $configuration
	 *
	 * @return void
	 */
	function preFileProcess(
		Resource\Service\FileProcessingService $fileProcessingService,
		Resource\Driver\DriverInterface $driver,
		Resource\ProcessedFile $processedFile,
		Resource\FileInterface $file,
		$context,
		array $configuration
	) {
		// optimize only FE images
		if ($context == 'Image.CropScaleMask' && TYPO3_MODE !== 'BE') {
			$properties = $processedFile->getProperties();
			if (
				!$processedFile->isProcessed() ||
				$processedFile->isNew() ||
				!$processedFile->exists() ||
				$processedFile->isOutdated() ||
				$properties['tinypng'] == 0
			) {
				$properties['tinypng'] = 0;
				$processedFile->updateProperties($properties);
				$this->processedFileRepository->update($processedFile);
				self::$shrink = true;
			} else {
				self::$shrink = false;
			}
		}
	}

	/**
	 * Post
	 *
	 * @param Resource\Service\FileProcessingService $fileProcessingService
	 * @param Resource\Driver\DriverInterface $driver
	 * @param Resource\ProcessedFile $processedFile
	 * @param Resource\FileInterface $file
	 * @param string $context
	 * @param array $configuration
	 *
	 * @return void
	 */
	function postFileProcess(
		Resource\Service\FileProcessingService $fileProcessingService,
		Resource\Driver\DriverInterface $driver,
		Resource\ProcessedFile $processedFile,
		Resource\FileInterface $file,
		$context,
		array $configuration
	) {
		if (self::$shrink) {
			$tmpFile = GeneralUtility::tempnam($processedFile->getName(), $processedFile->getExtension());
			$sourceFile = $processedFile->getForLocalProcessing(false);

			$this->tinypngService->shrinkImage($sourceFile, $tmpFile);

			$properties['reduction'] = filesize($sourceFile) - filesize($tmpFile);

			$processedFile->updateWithLocalFile($tmpFile);

			$properties['tinypng'] = 1;
			$processedFile->updateProperties($properties);
			$this->processedFileRepository->update($processedFile);
		}
	}
}
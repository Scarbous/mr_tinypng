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

use TYPO3\CMS\Core\Resource,
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
     * The ProcessedFileRepository
     *
     * @var \TYPO3\CMS\Core\Resource\ProcessedFileRepository
     * @inject
     */
    protected $processedFileRepository;

    /**
     * Inject the ProcessedFileRepository
     *
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
        $testi = $file->getMimeType();
        // optimize only FE images
        if ($context == 'Image.CropScaleMask' && TYPO3_MODE !== 'BE' &&
            ($file->getMimeType() == TinypngService::PNG || $file->getMimeType() == TinypngService::JPG)
        ) {
            $properties = $processedFile->getProperties();
            if (
                (
                    !$processedFile->isProcessed() ||
                    $processedFile->isNew() ||
                    !$processedFile->exists() ||
                    $processedFile->isOutdated() ||
                    $properties['reduced'] == 0
                ) &&
                $properties['reduce_it'] == 0
            ) {
                $properties['reduced'] = 0;
                $properties['reduce_it'] = 1;
                $processedFile->updateProperties($properties);
                $this->processedFileRepository->update($processedFile);
            }
        }
    }
}
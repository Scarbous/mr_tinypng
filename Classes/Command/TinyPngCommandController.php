<?php

namespace Scarbous\MrTinypng\CMS\Command;

use TYPO3\CMS\Core\Resource\ProcessedFile;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\CommandController;

class TinyPngCommandController extends CommandController
{


    /**
     * The processed file repository
     *
     * @var \Scarbous\MrTinypng\CMS\Core\Resource\ProcessedFileRepository
     * @inject
     */
    protected $processedFileRepository;

    /**
     * @var \Scarbous\MrTinypng\Service\TinypngService
     * @inject
     */
    protected $tinypngService;

    /**
     * Shrink images
     * @param int $limit
     * @return void
     */
    public function shrinkCommand($limit = 10)
    {
        $sumSourceSize = 0;
        $sumNewSize = 0;
        $sumReduction = 0;


        $files = $this->processedFileRepository->findNotReduced($limit);
        if ($files->count() == 0) {
            $this->outputLine("No Images to optimize");

            return;
        }

        $this->outputLine("Start optimizing Images");
        $this->output->progressStart($files->count());
        /** @var ProcessedFile $file */
        foreach ($files as $file) {
            $this->output->progressAdvance();
            $tmpFile = GeneralUtility::tempnam($file->getName(), $file->getExtension());

            $sourceFile = $file->getForLocalProcessing(false);
            $this->tinypngService->reduceImage($sourceFile, $tmpFile);

            $size = getimagesize($tmpFile);
            if ($size[0] > 0 && $size[1] > 0) {
                $properties['width'] = $size[0];
                $properties['height'] = $size[1];
                $sumSourceSize += $sourceSize = filesize($sourceFile);
                $sumNewSize += $newSize = filesize($tmpFile);

                $sumReduction += $properties['reduction'] = $sourceSize - $newSize;

                $file->updateWithLocalFile($tmpFile);

                $properties['reduced'] = 1;

                $file->updateProperties($properties);
                $this->processedFileRepository->update($file);
            }
        }
        $this->output->progressFinish();
        $this->outputLine("Finish optimizing Images");
        $this->outputLine("Source Size: <options=bold>$sumSourceSize</>");
        $this->outputLine("New Size: <options=bold>$sumNewSize</>");
        $this->outputLine("Reduction: <options=bold,underscore>$sumReduction</>");
    }
}
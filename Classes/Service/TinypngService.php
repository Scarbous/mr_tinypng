<?php

namespace Scarbous\MrTinypng\Service;

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

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class TinypngService
 * @package Scarbous\MrTinypng\Service
 * @author Sascha Heilmeier <s.heilmeier@misterknister.com>
 */
class TinypngService implements SingletonInterface
{
    /**
     * PNG mime-type
     */
    const PNG = 'image/png';

    /**
     * JPG mime-type
     */
    const JPG = 'image/jpeg';

    /**
     * Validate API
     *
     * @return mixed
     */
    function validate()
    {
        try {
            return \Tinify\validate();
        } catch (\Tinify\Exception $e) {
            $this->getLogManager()->error(
                'Tinify Exception! Please check your API-Key!',
                [
                    'Exception code' => $e->getCode(),
                    'Exception message' => $e->getMessage(),
                    'Exception trace' => $e->getTrace(),
                ]
            );

            return $e->getMessage();
        }
    }

    /**
     * Get compression count
     * @return mixed
     */
    function compressionCount()
    {
        return \Tinify\compressionCount();
    }

    /**
     * Reduce the Image
     *
     * @param string $source The source file path
     * @param string $target The target file path
     *
     * @return int|bool
     */
    public function reduceImage($source, $target = null)
    {

        try {
            if (in_array(mime_content_type($source), [self::JPG, self::PNG])) {
                $target = $target === null ? $source : $target;
                $sourceData = GeneralUtility::getURL($source);
                $targetData = \Tinify\fromBuffer($sourceData)->toBuffer();
                file_put_contents($target, $targetData);
                $reduction = strlen($sourceData) - strlen($targetData);
                unset($sourceData, $targetData);

                return $reduction;
            } else {
                return false;
            }
        } catch (\Tinify\Exception $e) {
            $this->getLogManager()->error(
                'Tinify Exception!',
                [
                    'Exception code' => $e->getCode(),
                    'Exception message' => $e->getMessage(),
                    'Exception trace' => $e->getTrace(),
                ]
            );

            return false;
        }
    }

    /**
     * @return \TYPO3\CMS\Core\Log\Logger
     */
    private function getLogManager()
    {
        $logger = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Core\Log\LogManager')->getLogger(__CLASS__);

        return $logger;
    }
}

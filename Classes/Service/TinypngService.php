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
	 * Validate API
	 *
	 * @return mixed
	 */
	function validate(){
		try {
			return \Tinify\validate();
		} catch(\Tinify\Exception $e) {
			 /** @var $logger \TYPO3\CMS\Core\Log\Logger */
		        $logger = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Core\Log\LogManager')->getLogger(__CLASS__);
		        $logger->error(
		            'Tinify Exception! Please check your API-Key!',
		            array(
		                'Exception code' => $e->getCode(),
		                'Exception message' => $e->getMessage(),
		                'Exception trace' => $e->getTrace(),
		            )
		        );
		        return $e->getMessage();
		}
	}

	/**
	 * Get compression count
	 * @return mixed
	 */
	function compressionCount(){
		return \Tinify\compressionCount();
	}
	/**
	 * Shrinks the Image
	 *
	 * @param string $source The source file path
	 * @param string $target The target file path
	 *
	 * @return int|bool
	 */
	public function shrinkImage($source, $target = NULL)
	{
		
	    	try {
			if (in_array(mime_content_type($source), self::getAllowedMimeTypes())) {
				$target = $target === NULL ? $source : $target;
				$sourceData = GeneralUtility::getURL($source);
				$targetData = \Tinify\fromBuffer($sourceData)->toBuffer();
				file_put_contents($target, $targetData);
				$reduction = strlen($sourceData) - strlen($targetData);
				unset($sourceData,$targetData);
				return $reduction;
			} else {
				return false;
			}
	    	} catch (\Tinify\Exception $e){
			/** @var $logger \TYPO3\CMS\Core\Log\Logger */
			$logger = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Core\Log\LogManager')->getLogger(__CLASS__);
			$logger->error(
				'Tinify Exception!',
				array(
					'Exception code' => $e->getCode(),
					'Exception message' => $e->getMessage(),
					'Exception trace' => $e->getTrace(),
				)
			);
			return false;
	        }
	}

    /**
     * Get list of allowed mime types from extension configuration
     * 
     * @return array
     */
    public static function getAllowedMimeTypes() {
        $extConfig = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['mr_tinypng']);
        return \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode('|', $extConfig['mimeTypes'], TRUE);
    }
}

<?php
namespace Scarbous\MrTinypng\CMS\Core\Resource;

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

/**
 * Class ProcessedFile
 *
 * @package Scarbous\MrTinypng\CMS\Core\Resource
 * @author Sascha Heilmeier <s.heilmeier@misterknister.com>
 */
class ProcessedFile extends \TYPO3\CMS\Core\Resource\ProcessedFile
{
	/**
	 * Manipulates the function for tinypng
	 *
	 * @return bool
	 */
	public function usesOriginalFile()
	{
		if (!parent::usesOriginalFile()) {
			return false;
		} else {
			if (!in_array($this->originalFile->getMimeType(), ['image/png', 'image/jpeg'])) {
				return true;
			}

			if ($this->originalFile->getSize() > 25000) {
				return false;
			}
		}

		return true;
	}
}
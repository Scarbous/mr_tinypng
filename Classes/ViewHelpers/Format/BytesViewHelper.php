<?php
namespace Scarbous\MrTinypng\ViewHelpers\Format;

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
	TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class BytesViewHelper
 *
 * @package Scarbous\MrTinypng\ViewHelpers\Format
 * @author Sascha Heilmeier <s.heilmeier@misterknister.com>
 */
class BytesViewHelper extends AbstractViewHelper
{

	/**
	 * The render Function
	 *
	 * @param int $bytes The size in Bytes
	 *
	 * @return string
	 */
	public function render($bytes = NULL)
	{
		if ($bytes === NULL) :
			$bytes = $this->renderChildren();
		endif;

		return GeneralUtility::formatSize($bytes);
	}
}
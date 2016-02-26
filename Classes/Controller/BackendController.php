<?php
namespace Scarbous\MrTinypng\Controller;

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

use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * Class BackendControlle
 *
 * @package Scarbous\MrTinypng\Controler
 * @author Sascha Heilmeier <s.heilmeier@misterknister.com>
 */
class BackendController extends ActionController
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
	 * The index action
	 *
	 * @return void
	 */
	function indexAction()
	{
		$reduction = $this->processedFileRepository->getReduction();
		$shrunkenFiles = $this->processedFileRepository->countShrunken();
		$notShrunkenFiles = $this->processedFileRepository->countNotShrunken();


		$this->view->assignMultiple([
			'tinypng' => [
				'validate'      => $this->tinypngService->validate(),
				'compressionCount' => $this->tinypngService->compressionCount(),
			],
			'shrunkenFiles'    => $shrunkenFiles,
			'notShrunkenFiles' => $notShrunkenFiles,
			'reduction'        => $reduction
		]);
	}
}
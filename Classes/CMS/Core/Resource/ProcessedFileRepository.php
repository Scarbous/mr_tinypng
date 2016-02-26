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

use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * Class ProcessedFileRepository
 *
 * @package Scarbous\MrTinypng\CMS\Core\Resource
 * @author Sascha Heilmeier <s.heilmeier@misterknister.com>
 */
class ProcessedFileRepository extends \TYPO3\CMS\Core\Resource\ProcessedFileRepository {
	/**
	 * Get all shrunken elements
	 *
	 * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	public function findShrunken(){

		$whereClause = 'tinypng=1';
		$rows = $this->databaseConnection->exec_SELECTgetRows('*', $this->table, $whereClause);

		$itemList = array();
		if ($rows !== null) {
			foreach ($rows as $row) {
				$itemList[] = $this->createDomainObject($row);
			}
		}
		return $itemList;
	}

	/**
	 * Get all not shrunken elements
	 *
	 * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	public function findNotShrunken(){

		$whereClause = 'tinypng=0';
		$rows = $this->databaseConnection->exec_SELECTgetRows('*', $this->table, $whereClause);

		$itemList = array();
		if ($rows !== null) {
			foreach ($rows as $row) {
				$itemList[] = $this->createDomainObject($row);
			}
		}
		return $itemList;
	}

	/**
	 * Count shrunken elements
	 *
	 * @return int
	 */
	public function countShrunken(){

		$whereClause = 'tinypng=1';
		$row = $this->databaseConnection->exec_SELECTgetSingleRow('COUNT(*) AS files ', $this->table, $whereClause);
		return $row['files'];
	}

	/**
	 * Count not shrunken elements
	 *
	 * @return int
	 */
	public function countNotShrunken(){

		$whereClause = 'tinypng=0';
		$row = $this->databaseConnection->exec_SELECTgetSingleRow('COUNT(*) AS files ', $this->table, $whereClause);
		return $row['files'];
	}

	/**
	 * Get reduction
	 *
	 * @return int
	 */
	public function getReduction(){

		$whereClause = 'tinypng=1';
		$row = $this->databaseConnection->exec_SELECTgetSingleRow('SUM(reduction) AS reduction', $this->table, $whereClause);
		return $row['reduction'];
	}
}
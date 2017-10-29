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

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Class ProcessedFileRepository
 *
 * @package Scarbous\MrTinypng\CMS\Core\Resource
 * @author Sascha Heilmeier <s.heilmeier@misterknister.com>
 */
class ProcessedFileRepository extends \TYPO3\CMS\Core\Resource\ProcessedFileRepository
{

    private function reducedQuery()
    {
        $queryBuilder = $this->getQueryBuilder();
        $queryBuilder->addSelect('*')
            ->where(
                $queryBuilder->expr()->eq(
                    'reduced',
                    $queryBuilder->createNamedParameter(1, \PDO::PARAM_INT)
                ),
                $queryBuilder->expr()->eq(
                    'reduce_it',
                    $queryBuilder->createNamedParameter(1, \PDO::PARAM_INT)
                )
            );

        return $queryBuilder;
    }

    /**
     * Get all reduced elements
     *
     * @return ObjectStorage
     */
    public function findReduced()
    {
        $queryBuilder = $this->reducedQuery();

        $rows = $queryBuilder->execute();

        $itemList = new ObjectStorage();
        if ($rows !== null) {
            foreach ($rows as $row) {
                $itemList->attach($this->createDomainObject($row));
            }
        }

        return $itemList;
    }

    /**
     * @return \TYPO3\CMS\Core\Database\Query\QueryBuilder
     */
    private function notReducedQuery()
    {
        $queryBuilder = $this->getQueryBuilder();
        $queryBuilder->addSelect('*')
            ->where(
                $queryBuilder->expr()->eq(
                    'reduced',
                    $queryBuilder->createNamedParameter(0, \PDO::PARAM_INT)
                ),
                $queryBuilder->expr()->eq(
                    'reduce_it',
                    $queryBuilder->createNamedParameter(1, \PDO::PARAM_INT)
                )
            );

        return $queryBuilder;
    }

    /**
     * Get all not reduced elements
     *
     * @param int $limit
     *
     * @return ObjectStorage
     */
    public function findNotReduced($limit = 0)
    {
        $queryBuilder = $this->notReducedQuery();
        if ($limit > 0) {
            $queryBuilder->setMaxResults($limit);
        }
        $rows = $queryBuilder->execute();

        $itemList = new ObjectStorage();
        if ($rows !== null) {
            foreach ($rows as $row) {
                $itemList->attach($this->createDomainObject($row));
            }
        }

        return $itemList;
    }

    /**
     * Count reduced elements
     *
     * @return int
     */
    public function countReduced()
    {
        $queryBuilder = $this->reducedQuery();

        return $queryBuilder->execute()->rowCount();
    }

    /**
     * Count not reduced elements
     *
     * @return int
     */
    public function countNotReduced()
    {
        $queryBuilder = $this->notReducedQuery();

        return $queryBuilder->execute()->rowCount();
    }

    /**
     * Get reduction
     *
     * @return int
     */
    public function getReduction()
    {
        $queryBuilder = $this->reducedQuery();
        $row = $queryBuilder->addSelectLiteral($queryBuilder->expr()->sum('reduction', 'reduction'))
            ->execute()
            ->fetch();

        return $row['reduction'];
    }


    /**
     * @return \TYPO3\CMS\Core\Database\Query\QueryBuilder
     */
    private function getQueryBuilder()
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($this->table);
        $queryBuilder->from($this->table);

        return $queryBuilder;
    }
}
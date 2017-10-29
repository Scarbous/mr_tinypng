<?php
/*
 * @var string $_EXTKEY
 */
/***************************************************************
 * Extension Manager/Repository config file for ext "mr_tinypng".
 *
 * Auto generated 07-03-2016 16:42
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = [
    'title' => 'Mr.tinypng',
    'description' => '',
    'category' => 'misc',
    'author' => 'Sascha Heilmeier',
    'author_email' => 'sheilmeier@gmail.com',
    'author_company' => '',
    'state' => 'beta',
    'clearCacheOnLoad' => 0,
    'version' => '0.3.3',
    'constraints' => [
        'depends' => [
            'typo3' => '7.6.0-8.7.99',
            'php' => '7.0.0-7.1.99',
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ],
    ],
    'uploadfolder' => false,
    'createDirs' => null,
    'clearcacheonload' => false,
];

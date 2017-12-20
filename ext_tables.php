<?php
/**
 * @var string $_EXTKEY
 */
defined('TYPO3_MODE') or die('Access denied.');

call_user_func(function ($extKey) {

    if (TYPO3_MODE === 'BE' && !(TYPO3_REQUESTTYPE & TYPO3_REQUESTTYPE_INSTALL)) {

        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][$extKey] =
            \Scarbous\MrTinypng\Command\TinyPngCommandController::class;

        /**
         * Register Backend Module
         */
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
            'Scarbous.' . $extKey,
            'tools',
            'backend',
            '',
            [
                'Backend' => 'index',
            ],
            [
                'access' => 'user,group',
                'icon' => 'EXT:' . $extKey . '/ext_icon.' .
                    (\TYPO3\CMS\Core\Utility\GeneralUtility::compat_version('7.0') ? 'svg' : 'png'),
                'labels' => 'LLL:EXT:' . $extKey . '/Resources/Private/Languages/locallang_backend.xlf',
            ]
        );
    }
}, $_EXTKEY);

<?php
/**
 * @var string $_EXTKEY
 */
defined('TYPO3_MODE') or die('Access denied.');


call_user_func(function ($extKey) {

    require_once(
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($extKey) . 'Resources/Private/Libraries/autoload.php'
    );

    $signalSlotDispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class);

    $signalSlotDispatcher->connect(
        \TYPO3\CMS\Core\Resource\ResourceStorage::class,
        \TYPO3\CMS\Core\Resource\Service\FileProcessingService::SIGNAL_PreFileProcess,
        \Scarbous\MrTinypng\SignalSlots\FileProcessingService::class,
        'preFileProcess'
    );

}, $_EXTKEY);

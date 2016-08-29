<?php
defined('TYPO3_MODE') or die('Access denied.');


require_once(
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'Resources/Private/Libraries/autoload.php'
);

$extConfig = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$_EXTKEY]);

if (!empty($extConfig['tinypngApiKey'])) {
	try {
		\Tinify\setKey($extConfig['tinypngApiKey']);
		\Tinify\validate();

		$signalSlotDispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class);

		$signalSlotDispatcher->connect(
			\TYPO3\CMS\Core\Resource\ResourceStorage::class,
			\TYPO3\CMS\Core\Resource\Service\FileProcessingService::SIGNAL_PreFileProcess,
			\Scarbous\MrTinypng\SignalSlots\FileProcessingService::class,
			'preFileProcess'
		);

		$signalSlotDispatcher->connect(
			\TYPO3\CMS\Core\Resource\ResourceStorage::class,
			\TYPO3\CMS\Core\Resource\Service\FileProcessingService::SIGNAL_PostFileProcess,
			\Scarbous\MrTinypng\SignalSlots\FileProcessingService::class,
			'postFileProcess'
		);

	} catch (\Tinify\Exception $e) {
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
	}
}



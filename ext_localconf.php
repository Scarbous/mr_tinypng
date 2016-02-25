<?php
defined('TYPO3_MODE') or die('Access denied.');


require_once(
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'Resources/Private/Libraries/autoload.php'
);

$extConfig = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$_EXTKEY]);

if(!empty($extConfig['tinypngApiKey'])) {
	try {
		\Tinify\setKey($extConfig['tinypngApiKey']);
		\Tinify\validate();

		$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'] = [
			\TYPO3\CMS\Core\Resource\Processing\LocalCropScaleMaskHelper::class => [
				'className' => \Scarbous\MrTinypng\CMS\Core\Resource\Processing\LocalCropScaleMaskHelper::class
			],
			\TYPO3\CMS\Core\Resource\ProcessedFile::class => [
				'className' => \Scarbous\MrTinypng\CMS\Core\Resource\ProcessedFile::class
			]
		];

	} catch(\Tinify\Exception $e) {
		throw new TYPO3\CMS\Install\Controller\Exception\RedirectException(
			'Tinify-API: '.$e->getMessage().' | Pleas check your API-Key',
			'1456417768'
		);
	}
}
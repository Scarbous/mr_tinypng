<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

if (TYPO3_MODE === 'BE' && !(TYPO3_REQUESTTYPE & TYPO3_REQUESTTYPE_INSTALL)) {

	/**
	 * Register Backend Module
	 */
	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
		'Scarbous.' . $_EXTKEY,
		'tools',
		'backend',
		'',
		[
			'Backend' => 'index',
		],
		[
			'access' => 'user,group',
			'icon'   => 'EXT:' . $_EXTKEY . '/ext_icon.'.
			(\TYPO3\CMS\Core\Utility\GeneralUtility::compat_version('7.0') ? 'svg' : 'png'),
			'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Languages/locallang_backend.xlf',
		]
	);
}
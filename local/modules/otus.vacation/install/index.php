<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Application;
use Bitrix\Main\Entity\Base;
use Bitrix\Main\Loader;
use Bitrix\Main\EventManager;

Loc::loadMessages(__FILE__);

class otus_vacation extends CModule
{
    public $MODULE_ID = 'otus.vacation';
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;

    function __construct()
    {
        $arModuleVersion = array();
        include(__DIR__ . '/version.php');

        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        $this->MODULE_NAME = Loc::getMessage('OTUS_VACATION_MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('OTUS_VACATION_MODULE_DESC');

        $this->PARTNER_NAME = Loc::getMessage('OTUS_VACATION_PARTNER_NAME');
        $this->PARTNER_URI = Loc::getMessage('OTUS_VACATION_PARTNER_URI');
    }

    private function getEntities()
    {
        return [
            \Otus\Vacation\Internal\VacationRequestTable::class,
            \Otus\Vacation\Internal\VacationItemTable::class,
            \Otus\Vacation\Internal\VacationRequestApprovalTable::class,
            \Otus\Vacation\Internal\VacationItemApprovalTable::class,
        ];
    }

    public function isVersionD7()
    {
        return CheckVersion(\Bitrix\Main\ModuleManager::getVersion('main'), '20.00.00');
    }

    public function GetPath($notDocumentRoot = false)
    {
        if ($notDocumentRoot) {
            return str_ireplace(Application::getDocumentRoot(), '', dirname(__DIR__));
        } else {
            return dirname(__DIR__);
        }
    }

    public function DoInstall()
    {
        global $APPLICATION;

        if ($this->isVersionD7()) {
            \Bitrix\Main\ModuleManager::registerModule($this->MODULE_ID);

            $this->InstallDB();
            $this->installFiles();
            $this->InstallEvents();
        } else {
            $APPLICATION->ThrowException(Loc::getMessage('OTUS_VACATION_INSTALL_ERROR_VERSION'));
        }
    }

    public function DoUninstall()
    {
        $this->UnInstallDB();
        $this->UnInstallEvents();

        \Bitrix\Main\ModuleManager::unRegisterModule($this->MODULE_ID);
    }

    public function installFiles($arParams = array())
    {
        $component_path = $this->GetPath() . '/install/components';
        
        if (\Bitrix\Main\IO\Directory::isDirectoryExists($component_path)) {
            CopyDirFiles($component_path, $_SERVER['DOCUMENT_ROOT'] . '/bitrix/components', true, true);
        } else {
            throw new \Bitrix\Main\IO\InvalidPathException($component_path);
        }

        $js_path = $this->GetPath() . '/install/js';
        
        if (\Bitrix\Main\IO\Directory::isDirectoryExists($js_path)) {
            CopyDirFiles($js_path, $_SERVER['DOCUMENT_ROOT'] . '/bitrix/js', true, true);
        } else {
            throw new \Bitrix\Main\IO\InvalidPathException($js_path);
        }

        CopyDirFiles($this->GetPath() . '/install/public', $_SERVER['DOCUMENT_ROOT'] . '/', true, true);

        $urlCondition = [
            'CONDITION' => '#^/vacation_request/#',
            'RULE' => '',
            'ID' => 'bitrix:blanc',
            'PATH' => '/vacation_request/index.php',
            'SORT' => 100,
        ];

        \Bitrix\Main\UrlRewriter::add(\SITE_ID, $urlCondition);

        $urlCondition = [
            'CONDITION' => '#^/vacation_schedule/#',
            'RULE' => '',
            'ID' => 'bitrix:blanc',
            'PATH' => '/vacation_schedule/index.php',
            'SORT' => 100,
        ];

        \Bitrix\Main\UrlRewriter::add(\SITE_ID, $urlCondition);
    }

    public function InstallDB()
    {
        Loader::includeModule($this->MODULE_ID);

        $entities = $this->getEntities();

        foreach ($entities as $entity) {
            if (!Application::getConnection($entity::getConnectionName())->isTableExists($entity::getTableName())) {
                Base::getInstance($entity)->createDbTable();
            }
        }
    }

    public function UnInstallDB()
    {
        Loader::includeModule($this->MODULE_ID);

        $connection = \Bitrix\Main\Application::getConnection();

        $entities = $this->getEntities();

        foreach ($entities as $entity) {
            if (Application::getConnection($entity::getConnectionName())->isTableExists($entity::getTableName())) {
                $connection->dropTable($entity::getTableName());
            }
        }
    }

    public function InstallEvents()
	{
		$eventManager = EventManager::getInstance();

		$eventManager->registerEventHandler(
			'main',
			'OnEpilog',
			$this->MODULE_ID,
			'\\Otus\\Vacation\\Handlers',
			'handleSidepanelLinks'
		);

		$eventManager->registerEventHandler(
            'crm',
            'onEntityDetailsTabsInitialized',
            $this->MODULE_ID,
			'\\Otus\\Vacation\\Crm\\Handlers',
			'updateTabs'
		);

        return true;
    }

    public function UnInstallEvents()
	{
		$eventManager = EventManager::getInstance();

		$eventManager->unRegisterEventHandler(
			'main',
			'OnEpilog',
			$this->MODULE_ID,
			'\\Otus\\Vacation\\Handlers',
			'handleSidepanelLinks'
		);

        $eventManager->unRegisterEventHandler(
            'crm',
            'onEntityDetailsTabsInitialized',
            $this->MODULE_ID,
            '\\Otus\\Vacation\\Crm\\Handlers',
            'updateTabs'
        );

        return true;
    }
}

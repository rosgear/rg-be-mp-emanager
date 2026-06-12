<?php
/**
 * Этот файл является частью расширения модуля веб-приложения RosGear.
 * 
 * @link https://rosgear.ru/
 * @copyright Copyright (c) 2015 RosGear
 * @license https://rosgear.ru/license/
 */

namespace Rg\Backend\Marketplace\ExtensionManager\Model;

use Ge;
use Ge\Panel\Data\Model\ArrayGridModel;
use Ge\ExtensionManager\ExtensionManager;

/**
 * Модель данных вывода сетки установленных и устанавливаемых расширений.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Rg\Backend\Marketplace\ExtensionManager\Model
 * @since 1.0
 */
class Grid extends ArrayGridModel
{
    /**
     * Менеджер расширений.
     * 
     * @see Grid::buildQuery()
     * 
     * @var ExtensionManager
     */
    protected ExtensionManager $extensions;

    /**
     * Карта идентификаторов установленных модулей в виде пар 
     * "идентификатор - конфигурация".
     *
     * @see \Ge\ModuleManager\ModuleRegistry::getMap()
     * @see Grid::buildQuery()
     * 
     * @var array
     */
    protected array $modules;

    /**
     * Имена установленных модулей в текущей локализации.
     * 
     * @see Grid::buildQuery()
     * 
     * @var array 
     */
    protected array $moduleNames;

    /**
     * {@inheritdoc}
     */
    public function getDataManagerConfig(): array
    {
        return [
            'fields' => [
                ['id'], // уникальный идентификатор записи в базе данных
                ['lock'], // системный
                ['extensionId'], // уникальный идентификатор расширения в приложении
                ['path'], // каталог расширения
                ['route'], // базовый маршрут расширения
                ['icon'], // значок расширения
                ['enabled'], // доступность
                ['name'], // имя расширения
                ['description'], // описание расширения
                ['namespace'], // пространство имён
                ['version'], // номер версии
                ['versionAuthor'], // автор версии
                ['versionDate'], // дата версии
                ['details'], // подробная информации о версии расширения
                ['infoUrl'], // маршрут к получению информации о расширении
                ['settingsUrl'], // маршрут к настройкам расширения
                ['extensionUrl'], // маршрут к расширению
                ['status'], // статус расширения: установлен (1), не установлен (0)
                ['clsCellLock'], // CSS-класс строки таблицы блокировки расширения
                ['rowCls'], // стиль строки
                ['installId'], // идентификатор установки расширения
                ['moduleName'], // имя расширения модуля
                ['moduleDesc'] // описание модуля расширения
            ],
            'filter' => [
                'type' => ['operator' => '='],
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        parent::init();

        $this
            ->on(self::EVENT_AFTER_DELETE, function ($someRecords, $result, $message) {
                // обновление конфигурации установленных модулей
                Ge::$app->extensions->update();
                // всплывающие сообщение
                $this->response()
                    ->meta
                        ->cmdPopupMsg($message['message'], $message['title'], $message['type']);
                /** @var \Ge\Panel\Controller\GridController $controller */
                $controller = $this->controller();
                // обновить список
                $controller->cmdReloadGrid();
            })
            ->on(self::EVENT_AFTER_SET_FILTER, function ($filter) {
                /** @var \Ge\Panel\Controller\GridController $controller */
                $controller = $this->controller();
                // обновить список
                $controller->cmdReloadGrid();
            });
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function buildQuery($builder): array
    {
        // карта идентификаторов установленных модулей
        $this->modules = Ge::$app->modules->getRegistry()->getMap();
        // менеджер расширений
        $this->extensions = Ge::$app->extensions;
        // имена установленных модулей в текущей локализации.
        $this->moduleNames = Ge::$app->modules->selectNames();

        /** @var \Ge\ExtensionManager\ExtensionRegistry $installed Установленные расширения модулей */
        $installed = $this->extensions->getRegistry();
        /** @var \Ge\ExtensionManager\ExtensionRepository $repository Репозиторий расширений модулей */
        $repository = $this->extensions->getRepository();

        // вид фильтра
        $type = $this->directFilter ? $this->directFilter['type']['value'] ?? '' : 'installed';
        switch($type) {
            // все расширения (установленные + не установленные)
            case 'all':
                return array_merge(
                    $installed->getListInfo(true, false, 'rowId', ['icon' => true, 'version' => true]),
                    $repository->find('Extension', 'nonInstalled', ['icon' => true, 'version' => true, 'config' => true, 'name' => true])
                );

            // установленные расширения
            case 'installed':
                return $installed->getListInfo(true, false, 'rowId', ['icon' => true, 'version' => true]);

            // не установленные расширения
            case 'nonInstalled':
                return $repository->find('Extension', 'nonInstalled', ['icon' => true, 'version' => true, 'config' => true, 'name' => true]);
        }
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function beforeFetchRow(mixed $row, int|string $rowKey): ?array
    {
        $moduleName   = ''; // имя расширения модуля
        $moduleDesc   = ''; // описание расширения модуля
        $details      = ''; // подробная информации о версии расширения
        $extensionUrl = '::disabled'; // маршрут к расширению
        $settingsUrl  = '::disabled'; // маршрут к настройкам расширения
        $infoUrl      = '::disabled'; // маршрут к получению информации о расширении
        $version      = $row['version']; // версия расширения
        $installId    = ''; // идентификатор установки модуля
        $namespace    = $row['namespace'] ?? '';  // пространство имён расширения
        $status       = ($row['rowId'] ?? 0) ? 1 : 0; // статус расширения
        $popupMenuItems = [[3, 'disabled'], [2, 'disabled'], [0, 'enabled']]; // контекстное меню записи

        // Определение версии расширения
        if ($version['version']) {
            $details = $version['version'];
            if ($version['versionDate']) {
                $details = $details . ' / ' . Ge::$app->formatter->toDate($version['versionDate']);
            }
        } else {
            if ($version['versionDate'])
                $details = $this->t('from') . ' ' . Ge::$app->formatter->toDate($version['versionDate']);
            else
                $details = $this->t('unknow');
        }

        /* Расширение установлено */
        if ($status === 1) {
            $id      = $row['rowId']; // уникальный идентификатор записи в базе данных
            $extId   = $row['id']; // уникальный идентификатор расширения в приложении
            $path    = $row['path']; // каталог расширения
            $route   = $row['baseRoute']; // базовый маршрут расширения
            $icon    = $row['icon']; // значок расширения
            $enabled = (int) $row['enabled']; // доступность
            $name    = $row['name']; // имя расширения
            $desc    = $row['description']; // описание расширения
            $use     = $row['use']; // назначение
            $lock    = $row['lock']; // системность
            $rowCls  = 'rg-mp-emanager-grid-row_installed'; // стиль строки
            // маршрут к расширению
            if ($route) {
                $extensionUrl = ($use === BACKEND ? '@backend/' : '@frontend/') . $route;
            }
            // маршрут к настройкам расширения
            if ($row['hasSettings']) {
                $settingsUrl = $row['settingsUrl'];
                $popupMenuItems[1][1] = 'enabled';
            }
            // маршрут к получению информации о расширении
            if ($row['hasInfo']) {
                $infoUrl = $row['infoUrl'];
                $popupMenuItems[0][1] = 'enabled';
            }
            // идентификатор модуля, которому принадлежит расширение 
            $moduleRowId = $row['moduleRowId'] ?? 0;
            if ($moduleRowId) {
                // если существует модуль текущего расширения
                if (isset($this->modules[$moduleRowId])) {
                    // определение имени и описания модуля
                    if (isset($this->moduleNames[$moduleRowId])) {
                        $moduleName = $this->moduleNames[$moduleRowId]['name'];
                        $moduleDesc = $this->moduleNames[$moduleRowId]['description'];
                    }
                }
            }

        /* Расширение не установлено */
        } else {
            $id      = uniqid(); // уникальный идентификатор записи в базе данных
            $extId   = $row['id']; // уникальный идентификатор расширения в приложении
            $path    = $row['path'] ?? ''; // каталог расширения
            $route   = $row['route'] ?? ''; // базовый маршрут расширения
            $icon    = $row['icon']; // значок расширения
            $enabled = -1; // доступность (скрыть)
            $name    = $row['name']; // имя расширения
            $desc    = $row['description']; // назначение
            $lock    = false; // системность
            $rowCls  = 'rg-mp-emanager-grid-row_notinstalled'; // стиль строки
            $installId = $this->extensions->encryptInstallId($path, $namespace);
            $popupMenuItems[2][1] = 'disabled';
        }

        return [
            'id'             => $id, // уникальный идентификатор записи в базе данных
            'lock'           => $lock, // системность
            'extensionId'    => $extId, // уникальный идентификатор расширения в приложении
            'path'           => $path, // каталог расширения
            'route'          => $route, // базовый маршрут расширения
            'icon'           => $icon, // значок расширения
            'enabled'        => $enabled, // доступность
            'name'           => $name, // имя расширения
            'description'    => $desc, // описание расширения
            'namespace'      => $namespace, // пространство имён
            'version'        => $version['version'], // номер версии
            'versionAuthor'  => $version['author'], // автор версии
            'versionDate'    => $version['versionDate'], // дата версии
            'details'        => $details, // подробная информации о версии расширения
            'infoUrl'        => $infoUrl, // маршрут к получению информации о расширении
            'settingsUrl'    => $settingsUrl, // маршрут к настройкам расширения
            'extensionUrl'   => $extensionUrl, // маршрут к расширению
            'status'         => $status, // статус расширения: установлен (1), не установлен (0)
            'clsCellLock'    => $lock ? 'g-cell-lock' : '', // CSS-класс строки таблицы блокировки расширения
            'popupMenuTitle' => $name, // заголовок контекстного меню записи
            'popupMenuItems' => $popupMenuItems, // доступ к элементам контекстного меню записи
            'rowCls'         => $rowCls, // стиль строки
            'installId'      => $installId, // идентификатор установки расширения
            'moduleName'     => $moduleName, // имя расширения модуля
            'moduleDesc'     => $moduleDesc // описание модуля расширения
        ];
    }
}

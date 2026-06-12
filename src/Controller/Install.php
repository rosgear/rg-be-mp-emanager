<?php
/**
 * Этот файл является частью расширения модуля веб-приложения RosGear.
 * 
 * @link https://rosgear.ru/
 * @copyright Copyright (c) 2015 RosGear
 * @license https://rosgear.ru/license/
 */

namespace Rg\Backend\Marketplace\ExtensionManager\Controller;

use Ge;
use Ge\Panel\Http\Response;
use Ge\Mvc\Module\BaseModule;
use Ge\Panel\Controller\FormController;
use Rg\Backend\Marketplace\ExtensionManager\Widget\InstallWindow;

/**
 * Контроллер установки расширения.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Rg\Backend\Marketplace\ExtensionManager\Controller
 * @since 1.0
 */
class Install extends FormController
{
    /**
     * {@inheritdoc}
     * 
     * @var BaseModule|\Rg\Backend\Marketplace\ExtensionManager\Extension
     */
    public BaseModule $module;

    /**
     * {@inheritdoc}
     * 
     * @return InstallWindow
     */
    public function createWidget(): InstallWindow
    {
        return new InstallWindow();
    }

    /**
     * Действие "complete" завершает установку расширения.
     * 
     * @return Response
     */
    public function completeAction(): Response
    {
        // добавляем шаблон локализации для установки (см. ".extension.php")
        $this->module->addTranslatePattern('install');

        /** @var \Ge\ExtensionManager\ExtensionManager */
        $extensions = Ge::$app->extensions;
        /** @var Response $response */
        $response = $this->getResponse();

        /** @var null|string Идентификатор установки расширения */
        $installId = Ge::$app->request->post('installId');

        /** @var string|array Расшифровка идентификатора установки расширения */
        $decrypt = $extensions->decryptInstallId($installId);
        if (is_string($decrypt)) {
            $response
                ->meta->error($decrypt);
            return $response;
        }

        // если расширение не имеет установщика "Installer\Installer.php"
        if (!$extensions->installerExists($decrypt['path'])) {
            $response
                ->meta->error($this->module->t('The extension installer at the specified path "{0}" does not exist', [$decrypt['path']]));
            return $response;
        }
        
        // каждое расширенние обязано иметь установщик, управление установщиком передаётся текущему модулю
        /** @var \Ge\ExtensionManager\ExtensionInstaller $installer Установщик расширения */
        $installer = $extensions->getInstaller([
            'module'    => $this->module, 
            'namespace' => $decrypt['namespace'],
            'path'      => $decrypt['path'], 
            'installId' => $installId
        ]);

        // если установщик не создан
        if ($installer === null) {
            $response
                ->meta->error($this->t('Unable to create extension installer'));
            return $response;
        }

        // устанавливает расширение
        if ($installer->install()) {
            $response
                ->meta
                    ->cmdPopupMsg(
                        $this->module->t('Extension installation "{0}" completed successfully', [$installer->info['name']]),
                        $this->t('Installing'),
                        'accept'
                    )
                    ->cmdReloadGrid($this->module->viewId('grid'));
        } else {
            $response
                ->meta->error($installer->getError());
        }
        return $response;
    }

    /**
     * Действие "view" выводит интерфейс установщика расширения.
     * 
     * @return Response
     */
    public function viewAction(): Response
    {
        // добавляем шаблон локализации для установки (см. ".extension.php")
        $this->module->addTranslatePattern('install');

        /** @var \Ge\ExtensionManager\ExtensionManager Менеджер расширений */
        $extensions = Ge::$app->extensions;
        /** @var Response $response */
        $response = $this->getResponse();

        /** @var null|string $installId Идентификатор установки расширения */
        $installId = Ge::$app->request->post('installId');

        /** @var string|array $decrypt Расшифровка идентификатора установки расширения */
        $decrypt = $extensions->decryptInstallId($installId);
        if (is_string($decrypt)) {
            $response
                ->meta->error($decrypt);
            return $response;
        }

        // если модуль не имеет установщика "Installer\Installer.php"
        if (!$extensions->installerExists($decrypt['path'])) {
            $response
                ->meta->error($this->module->t('The extension installer at the specified path "{0}" does not exist', [$decrypt['path']]));
            return $response;
        }

        // каждое расширение обязано иметь установщик, управление установщиком передаётся текущему модулю
        /** @var \Ge\ExtensionManager\ExtensionInstaller|null $installer Установщик расширения */
        $installer = $extensions->getInstaller([
            'module'    => $this->module, 
            'namespace' => $decrypt['namespace'],
            'path'      => $decrypt['path'], 
            'installId' => $installId
        ]);

        // если установщик не создан
        if ($installer === null) {
            $response
                ->meta->error($this->t('Unable to create extension installer'));
            return $response;
        }

        /** @var null|\Ge\Panel\Widget\BaseWidget|\Ge\View\Widget $widget */
        $widget = $installer->getWidget();
        // если установщик не имеет виджет
        if ($widget === null) {
            /** @var InstallWindow $widget */
            $widget = $this->getWidget();
        }
        $widget->info = $installer->getExtensionInfo();

        // проверка конфигурации устанавливаемого расширения
        if (!$installer->validateInstall()) {
            $widget->notice = $installer->getError();
        }

        // если была ошибка при формировании расширения
        if ($widget === false) {
            return $response;
        }

        $response
            ->setContent($widget->run())
            ->meta
                ->addWidget($widget);
        return $response;
    }
}

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
use Rg\Backend\Marketplace\ExtensionManager\Widget\UpdateWindow;

/**
 * Контроллер обновления расширения модуля.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Rg\Backend\Marketplace\ExtensionManager\Controller
 * @since 1.0
 */
class Update extends FormController
{
    /**
     * @var BaseModule|\Rg\Backend\Marketplace\ExtensionManager\Extension
     */
    public BaseModule $module;

    /**
     * {@inheritdoc}
     */
    public function createWidget(): UpdateWindow
    {
        /** @var UpdateWindow $window Окно обновления расширения (Ext.window.Window Sencha ExtJS) */
        $window = new UpdateWindow();
        $window->title = $this->t('{update.title}');
        // шаги обновления модуля: ['заголовок', выполнен]
        $window->steps->extract  = [$this->t('Extract files from the update package'), true];
        $window->steps->copy     = [$this->t('Copying files to the extension repository'), true];
        $window->steps->validate = [$this->t('Checking extension files and configuration'), true];
        $window->steps->update   = [$this->t('Update extension data'), false];
        $window->steps->register = [$this->t('Extension registry update'), false];

        // панель формы (Ge.view.form.Panel GeJS)
        $window->form->router['route'] = $this->module->route('/update');
        return $window;
    }

    /**
     * Действие "complete" завершает обновление расширения модуля.
     * 
     * @return Response
     */
    public function completeAction(): Response
    {
        // добавляем шаблон локализации для обновления (см. ".extension.php")
        $this->module->addTranslatePattern('update');

        /** @var \Ge\ExtensionManager\ExtensionManager Менеджер расширений */
        $manager = Ge::$app->extensions;
        /** @var Response $response */
        $response = $this->getResponse();

        /** @var null|string $extensionId Идентификатор установленного расширения */
        $extensionId = Ge::$app->request->post('id');
        if (empty($extensionId)) {
            $response
                ->meta->error(Ge::t('backend', 'Invalid argument "{0}"', ['id']));
            return $response;
        }

        /** @var null|array $moduleParams Параметры установленного модуля */
        $extParams = $manager->getRegistry()->get($extensionId);
        // модуль с указанным идентификатором не установлен
        if ($extParams === null) {
            $response
                ->meta->error($this->module->t('There is no extension with the specified id "{0}"', [$extensionId]));
            return $response;
        }

        // если модуль не имеет установщика "Installer\Installer.php"
        if (!$manager->installerExists($extParams['path'])) {
            $response
                ->meta->error($this->module->t('The extension installer at the specified path "{0}" does not exist', [$extParams['path']]));
            return $response;
        }

        // каждое расширение обязано иметь установщик, управление установщиком передаётся текущему модулю
        /** @var \Ge\ExtensionManager\ExtensionInstaller $installer Установщик расширения */
        $installer = $manager->getInstaller([
            'module'    => $this->module, 
            'namespace' => $extParams['namespace'],
            'path'      => $extParams['path'],
        ]);

        // если установщик не создан
        if ($installer === null) {
            $response
                ->meta->error($this->t('Unable to create extension installer'));
            return $response;
        }

        // обновляет расширение
        if ($installer->update()) {
            $info = $installer->getExtensionInfo();
            $response
                ->meta
                    ->cmdPopupMsg(
                        $this->module->t('Update of extension "{0}" completed successfully', [$info ? $info['name'] : SYMBOL_NONAME]),
                        $this->t('Updating'),
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
     * Действие "view" выводит интерфейс установщика модуля.
     * 
     * @return Response
     */
    public function viewAction(): Response
    {
        // добавляем шаблон локализации для обновления (см. ".extension.php")
        $this->module->addTranslatePattern('update');

        /** @var \Ge\ExtensionManager\ExtensionManager $manager Менеджер расширений */
        $manager = Ge::$app->extensions;
        /** @var Response $response */
        $response = $this->getResponse();

        /** @var null|string $extensionId Идентификатор установленного расширения */
        $extensionId = Ge::$app->request->post('id');
        if (empty($extensionId)) {
            $response
                ->meta->error(Ge::t('backend', 'Invalid argument "{0}"', ['id']));
            return $response;
        }

        /** @var null|array $extParams Параметры установленного расширения */
        $extParams = $manager->getRegistry()->get($extensionId);
        // расширение с указанным идентификатором не установлено
        if ($extParams === null) {
            $response
                ->meta->error($this->module->t('There is no extension with the specified id "{0}"', [$extensionId]));
            return $response;
        }

        // если расширение не имеет установщика "Installer\Installer.php"
        if (!$manager->installerExists($extParams['path'])) {
            $response
                ->meta->error($this->module->t('The extension installer at the specified path "{0}" does not exist', [$extParams['path']]));
            return $response;
        }

        // каждое расширение обязано иметь установщик, управление установщиком передаётся текущему модулю
        /** @var \Ge\ExtensionManager\ExtensionInstaller $installer Установщик расширения */
        $installer = $manager->getInstaller([
            'module'    => $this->module, 
            'namespace' => $extParams['namespace'],
            'path'      => $extParams['path']
        ]);

        // если установщик не создан
        if ($installer === null) {
            $response
                ->meta->error($this->t('Unable to create extension installer'));
            return $response;
        }

        // проверка конфигурации обновляемого расширения
        if (!$installer->validateUpdate()) {
            $response
                ->meta->error(
                    $this->module->t('Unable to update the extension, there were errors in the files of the new version of the extension')
                    . '<br>' . $installer->getError()
                );
            return $response;
        }

        /** @var UpdateWindow $widget */
        $widget = $installer->getWidget();
        // если установщик не имеет виджет
        if ($widget === null) {
            $widget = $this->getWidget();
        }
        $widget->info = $installer->getExtensionInfo();

        // если была ошибка при формировании виджета
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

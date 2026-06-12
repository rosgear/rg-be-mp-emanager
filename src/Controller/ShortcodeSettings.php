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

/**
 * Контроллер настройки шорткода расширения модуля.
 * 
 * Действия контроллера:
 * - view, вывод интерфейса настроек шорткода расширения модуля.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Rg\Backend\Marketplace\ExtensionManager\Controller
 * @since 1.0
 */
class ShortcodeSettings extends FormController
{
    /**
     * {@inheritdoc}
     * 
     * @var BaseModule|\Rg\Backend\Marketplace\ExtensionManager\Extension
     */
    public BaseModule $module;

    /**
     * {@inheritdoc}
     */
    public function translateAction(mixed $params, ?string $default = null): ?string
    {
        switch ($this->actionName) {
            // вывод интерфейса
            case 'view':
                return Ge::t(BACKEND, "{{$this->actionName} settings action}");

            default:
                return parent::translateAction(
                    $params,
                    $default ?: Ge::t(BACKEND, "{{$this->actionName} settings action}")
                );
        }
    }

    /**
     * Возвращает идентификатор выбранного расширения модуля.
     *
     * @return int
     */
    public function getIdentifier(): int
    {
        return (int) Ge::$app->router->get('id');
    }

    /**
     * Действие "view" выводит интерфейс настроек шорткода расширения модуля.
     * 
     * @return Response
     */
    public function viewAction(): Response
    {
        /** @var Response $response */
        $response = $this->getResponse();

        /** @var null|int $id Идентификатор расширения модуля */
        $id = $this->getIdentifier();
        if (empty($id)) {
            return $this->errorResponse(
                GE_MODE_DEV ?
                    Ge::t('app', 'Parameter "{0}" not specified', ['id']) :
                    $this->module->t('Unable to show extension shortcode settings')
            );
        }

        /** @var null|string $tagName Имя тега */
        $tagName = Ge::$app->request->getQuery('name');
        if (empty($tagName)) {
            return $this->errorResponse(
                GE_MODE_DEV ?
                    Ge::t('app', 'Parameter "{0}" not specified', ['name']) :
                    $this->module->t('Unable to show extension shortcode settings')
            );
        }

        /** @var null|array $extParams Параметры расширения модуля */
        $extParams = Ge::$app->extensions->getRegistry()->getAt($id);
        if ($extParams === null) {
            return $this->errorResponse(
                GE_MODE_DEV ?
                    Ge::t('app', 'There is no widget with the specified id "{0}"', ['$id']) :
                    $this->module->t('Unable to show extension shortcode settings')
            );
        }

        /** @var null|array $install Параметры установки расширения модуля */
        $install = Ge::$app->extensions->getRegistry()->getConfigInstall($id);
        // если параметры установки не найдены
        if ($install === null) {
            return $this->errorResponse(
                GE_MODE_DEV ?
                    Ge::t('app', 'There is no widget with the specified id "{0}"', ['$id']) :
                    $this->module->t('Unable to show extension shortcode settings')
            );
        }

        /** @var array|null $shortcode Параметры указанного шорткода расширения модуля */
        $shortcode = $install['editor']['shortcodes'][$tagName] ?? null;
        if (empty($shortcode)) {
            return $this->errorResponse(
                GE_MODE_DEV ?
                    Ge::t('app', 'Parameter passed incorrectly "{0}"', ['shortcodes[' . $tagName . ']']) :
                    $this->module->t('Unable to show extension shortcode settings')
            );
        }

        // если нет настроек шорткода
        if (empty($shortcode['settings'])) {
            return $this->errorResponse(
                GE_MODE_DEV ?
                    Ge::t('app', 'The value for parameter "{0}" is missing', ['shortcodes[settings]']) :
                    $this->module->t('Unable to show extension shortcode settings')
            );
        }

        // для доступа к пространству имён объекта
        Ge::$loader->addPsr4($extParams['namespace']  . NS, Ge::$app->modulePath . $extParams['path'] . DS . 'src');

        $settingsClass = $extParams['namespace'] . NS . $shortcode['settings'];
        if (!class_exists($settingsClass)) {
            return $this->errorResponse(
                $this->module->t('Unable to create widget object "{0}"', [$settingsClass])
            );
        }

        // добавляем шаблон локализации расширения модуля (которому принадлежит шорткод)
        $category = Ge::$app->translator->getCategory($this->module->id);
        // ключ шаблона при подключении не имеет значение
        $category->patterns['shortcodeSettings'] = [
            'basePath' => Ge::$app->modulePath . $extParams['path'] . DS . 'lang',
            'pattern'  => 'text-%s.php',
        ];
        $this->module->addTranslatePattern('shortcodeSettings');

        /** @var object|Ge\Panel\Widget\ShortcodeSettingsWindow $widget Виджет настроек шорткода */
        $widget = Ge::createObject($settingsClass);
        if ($widget instanceof Ge\Panel\Widget\ShortcodeSettingsWindow) {
            $widget->form->controller = 'rg-mp-emanager-shortcodesettings';
            $widget
                ->setNamespaceJS('Rg.be.mp.emanager')
                ->addRequire('Rg.be.mp.emanager.ShortcodeSettingsController' . (GE_DEBUG ? '-debug' : ''));
        }

        $response
            ->setContent($widget->run())
            ->meta
                ->addWidget($widget);
        return $response;
    }
}

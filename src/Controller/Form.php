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
use Ge\Mvc\Module\BaseModule;
use Ge\Panel\Helper\ExtForm;
use Ge\Panel\Widget\EditWindow;
use Ge\Panel\Widget\Form as WForm;
use Ge\Panel\Controller\FormController;

/**
 * Контроллер редактирования расширения.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Rg\Backend\Marketplace\ExtensionManager\Controller
 * @since 1.0
 */
class Form extends FormController
{
    /**
     * {@inheritdoc}
     * 
     * @var BaseModule|\Rg\Backend\Marketplace\ExtensionManager\Extension
     */
    public BaseModule $module;

    /**
     * Возвращает идентификатор выбранного расширения.
     *
     * @return int
     */
    public function getIdentifier(): int
    {
        return (int) Ge::$app->router->get('id');
    }

    /**
     * {@inheritdoc}
     */
    public function createWidget(): EditWindow
    {
        /** @var null|array $extensionInfo Информация о расширении модуля */
        $extensionInfo = null;
        // идентификатор модулей
        if ($identifier = $this->getIdentifier()) {
            $extensionInfo = Ge::$app->extensions->getRegistry()->getInfo($identifier);
        }

        /** @var EditWindow $window */
        $window = parent::createWidget();

        // окно компонента (Ext.window.Window Sencha ExtJS)
        if ($extensionInfo) {
            $window->icon = $extensionInfo['icon'];
        }
        $window->ui = 'install';
        $window->title = $this->t('{form.title}');
        $window->titleTpl = sprintf('%s <span>%s</span>', $this->t('{form.title}'),  $this->t('{form.subtitle}'));
        $window->width = 520;
        $window->autoHeight = true;
        $window->layout = 'fit';
        $window->resizable = false;

        // панель формы (Ge.view.form.Panel GeJS)
        $window->form->autoScroll = true;
        $window->form->router->route = $this->module->route('/form');
        // определяем свой набор кнопок
        $window->form->setStateButtons(WForm::STATE_UPDATE, ['help' => ['subject' => 'edit'], 'save', 'cancel']);
        $viewFile = '/form';
        // если есть информация о модуле
        if ($extensionInfo) {
            // если расширение системное (настройки нельзя менять)
            $lock = (bool) ($extensionInfo['lock'] ?? false);
            $viewFile = $lock ? 'form-lock' : $viewFile;
        }
        $window->form->loadJSONFile($viewFile, 'items', [
                // языковая панель вкладок с полями
                '@languageTabs' => ExtForm::languageTabs(function ($tag) {
                    return [
                        [
                            'xtype'      => 'textfield',
                            'fieldLabel' => '#Name',
                            'name'       => $tag ? 'locale[' . $tag. '][name]' : 'name',
                            'labelWidth' => 105,
                            'anchor'     => '100%',
                            'width'      => 447,
                            'maxLength'  => 255,
                            'allowBlank' => false
                        ],
                        [
                            'xtype'      => 'textfield',
                            'fieldLabel' => '#Description',
                            'name'       => $tag ? 'locale[' . $tag. '][description]' : 'description',
                            'labelWidth' => 105,
                            'anchor'     => '100%',
                            'width'      => 447,
                            'maxLength'  => 255,
                            'allowBlank' => false
                        ],
                    ];
                }, true, [], ['layout' => 'anchor'])
            ]
        );
        return $window;
    }
}

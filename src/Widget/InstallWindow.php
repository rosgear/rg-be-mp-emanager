<?php
/**
 * Этот файл является частью пакета GePanel.
 * 
 * @link https://rosgear.ru//framework/
 * @copyright Copyright (c) 2015 RosGear
 * @license https://rosgear.ru/license/
 */

namespace Rg\Backend\Marketplace\ExtensionManager\Widget;

use Ge;
use Ge\Helper\Html;
use Ge\Panel\Widget\Form;
use Ge\Panel\Widget\Window;
use Ge\Panel\Helper\ExtCombo;
use Ge\Permissions\Mbac\Model;

/**
 * Виджет формирования интерфейса окна установки расширения модуля.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Rg\Backend\Marketplace\ExtensionManager\Widget
 * @since 1.0
 */
class InstallWindow extends Window
{
    /**
     * Виджет для формирования интерфейса формы.
     * 
     * @var Form
     */
    public Form $form;

    /**
     * Информация о расширении модуля.
     * 
     * @var array
     */
    public array $info = [];

    /**
     * @var string
     */
    public string $notice = '';

    /**
     * Вкладки окна после рендера.
     * 
     * @see ExtensionInstallWidget::renderTabs()
     * 
     * @var array
     */
    protected array $tabs = [];

    /**
     * {@inheritdoc}
     */
    public array $requires = [
        'Ge.view.window.Window',
        'Ge.view.form.Panel',
        'Ge.view.form.field.TreeComboBox'
    ];

    /**
     * {@inheritdoc}
     */
    public array $css = ['/install.css'];

    /**
     * {@inheritdoc}
     */
    protected function init(): void
    {
        parent::init();

        // панель формы (Ge.view.form.Panel GeJS)
        $this->form = new Form([
            'id'     => 'iform', // => rg-mp-emanager-iform
            'router' => [
                'route' => $this->creator->route('/install'),
                'state' => Form::STATE_CUSTOM,
                'rules' => [
                    'complete' => '{route}/complete'
                ] 
            ],
            'items' => [
                [
                    'xtype'          => 'tabpanel',
                    'anchor'         => '100% 100%',
                    'enableTabScroll'=> true,
                    'items'          => &$this->tabs
                ]
            ]
        ], $this);

        // окно (Ext.window.Window Sencha ExtJS)
        $this->id        = 'rg-mp-emanager-iwindow';
        $this->cls       = 'g-window_install';
        $this->width     = 600;
        $this->height    = 800;
        $this->layout    = 'fit';
        $this->resizable = false;
        $this->ui        = 'install';
        $this->items = [$this->form];
        $this->responsiveConfig = [
            'height < 800' => ['height' => '99%'],
            'width < 600' => ['width' => '99%'],
        ];
    }

    /**
     * Возвращает набор полей "Роли пользователей".
     * 
     * @param arary|null $installConfig Параметры конфигурации установки.
     * 
     * @return array
     */
    protected function getRolesFieldset(?array $installConfig): array
    {
        /**
         * @var array $permissions Разрешения ['any', 'read', 'write'] => 'any,read,write'
         */
        $permissions = $installConfig['permissions'] ?? [];
        if ($permissions)
            $permissions = implode(',', $permissions);
        else
            $permissions = 'any';

        $fieldset = [
            'xtype'    => 'fieldset',
            'title'    => '#User roles',
            'defaults' => [
                'xtype'    => 'checkbox',
                'ui'       => 'switch',
                'labelAlign' => 'right',
                'labelWidth' => 110,
                'checked' => false
            ],
            'items' => []
        ];

        /** @var array $roles Все роли пользователей */
        $roles = (new Model\Role())->fetchAll();
        foreach ($roles as $role) {
            $fieldset['items'][] = [
                'boxLabel' => Html::tag('div', $role['description'], ['class' => 'g-form__checkbox-info_desk text_description']),
                'name'     => 'roles[' . $role['id'] . ':' . $permissions . ']',
                'tooltip'  => $role['description'],
            ];
        }
        return $fieldset;
    }

    /**
     * Возвращает набор полей "Права доступа расширения".
     * 
     * @param arary|null $installConfig Параметры конфигурации установки.
     * 
     * @return array
     */
    protected function getExtPermissionsFieldset(?array $installConfig): array
    {
        $fieldset = [
            'xtype'    => 'fieldset',
            'title'    => '#Extension permissions',
            'defaults' => [
                'xtype'      => 'displayfield',
                'ui'         => 'parameter',
                'labelAlign' => 'right',
                'labelWidth' => 100
            ],
            'items' => []
        ];
        $permissions = $installConfig['permissions'];
        // если нет разрешений
        if (empty($permissions)) {
            $fieldset['items'][] = [
                'xtype' => 'box',
                'html'  => $this->creator->t('[ unknown ]')
            ];
            return $fieldset;
        }

        // разрешения расширения для текущей локализации
        $transPermissions = Ge::t($installConfig['id'] . Ge::$app->language->locale, '{permissions}');
        // если перевода нет
        if ($transPermissions === '{permissions}') {
            $transPermissions = [];
        }
        // особые разрешения (info, settings, recordRls, writeAudit, viewAudit) менеджера данных для текущей локализации
        $specPermissions  = Ge::t('backend', '{dataManagerPermissions}');
        $transPermissions = $transPermissions ? array_merge($transPermissions, $specPermissions) : $specPermissions;
        // разрешения расширения
        foreach ($permissions as $permission) {
            if ($transPermissions) {
                // если в локализации не забыли указать разрешение
                if (isset($transPermissions[$permission])) {
                    // имя разрешения и описание
                    $names = $transPermissions[$permission];
                    $name = $names[0];
                    $description = $names[1] ?? '';
                // если в локализации забыли указать разрешение
                } else {
                    $name = ucfirst($permission);
                    $description = '';
                }
            } else {
                $name = ucfirst($permission);
                $description = '';
            }
            if ($description) {
                $name .= ' <small style="text-transform:lowercase;color:#acacac">(' . $description . ')</small>';
            }
            $fieldset['items'][] = [
                'fieldLabel' => $permission,
                'value'      => $name
            ];
        }
        return $fieldset;
    }

    /**
     * Возвращает набор полей "Куда устанавливать расширение".
     * 
     * @return array
     */
    protected function getInterfaceFieldset(): array
    {
        return [
            'xtype'    => 'fieldset',
            'title'    => '#After installation, the extension will be added to',
            'defaults' => [
                'labelAlign' => 'right',
                'anchor'     => '100%',
                'labelWidth' => 120
            ],
            'items'=> [
                ExtCombo::treeRemote(
                    '#Partition bar',
                    'partitionbar',
                    [
                        'proxy' => [
                            'url' => ['partitionbar/trigger/combo', BACKEND],
                            'extraParams' => [
                                'combo'   => 'partitionbar',
                                'noneRow' => 1
                            ]
                        ]
                    ],
                    [
                        'allowBlank' => false,
                        'editable'   => false
                    ]
                )
            ]
        ];
    }

    /**
     * Возвращает набор полей с именем и описанием расширения в указанных локализациях.
     * 
     * @param array $names Имена и описание расширения в указанных локализациях.
     * 
     * @return array
     */
    protected function getNamesFieldset(array $params): array
    {
        $fieldset = [
            'xtype'    => 'fieldset',
            'title'    => '#Extension name in installed locales',
            'defaults' => [
                'xtype'      => 'displayfield',
                'ui'         => 'parameter',
                'labelAlign' => 'right',
                'labelWidth' => 105
            ],
            'items' => []
        ];
        $installConfig = $params['install'];
        $extensionConfig  = $params['config'];
        /** @var \Ge\I18n\ISO\ISO $iso */
        $iso = Ge::$app->locale->getISO();
        // перечень имён и описаний модуля
        foreach ($installConfig['locales'] as $locale) {
            // информация о локале
            $localeInfo = $iso->locales->get($locale);
            if ($localeInfo !== null && isset($localeInfo['nativeName']['language']))
                $languageName = $localeInfo['nativeName']['language'];
            else
                $languageName = $this->creator->t('Unknown');
            $fieldset['items'][] = [
                'xtype' => 'label',
                'ui'    => 'header-line',
                'text'  => $languageName . ' (' . $locale . ')'
            ];
            try {
                // создаём локализатор для выбранной локали расширения
                $extensionConfig['translator']['locale'] = $locale;
                // имя категории сообщений переводчика (в данном случаи для каждой локали расширения своя категория)
                $category = $installConfig['id'] . $locale;
                Ge::$app->translator->addCategory($category, $extensionConfig['translator']);
                $name = Ge::t($category, '{name}');
                // если названия нет в локализации, тогда указываем на это
                if ($name === '{name}') {
                    $name = SYMBOL_NONAME;
                }
                $description = Ge::t($category, '{description}');
                // если описания нет в локализации, тогда указываем на это
                if ($description === '{description}') {
                    $description = SYMBOL_NONAME;
                }
                $fieldset['items'][] = [
                    'xtype'      => 'displayfield',
                    'ui'         => 'parameter',
                    'fieldLabel' => '#Name',
                    'value'      => $name
                ];
                $fieldset['items'][] = [
                    'xtype'      => 'displayfield',
                    'ui'         => 'parameter',
                    'fieldLabel' => '#Description',
                    'value'      => $description
                ];
                // если язык расширения не установлен
                if (!Ge::$app->language->available->has($locale, 'locale')) {
                    $fieldset['items'][] = [
                        'xtype' => 'label',
                        'style' => 'color:#e78155;padding-left:45px',
                        'text' => $this->creator->t('will not be added because not installed', [$languageName . ' (' . $locale . ')'])
                    ];
                }
            // если файл локализации не найден
            } catch (\Exception $error) {
                $fieldset['items'][] = [
                    'xtype'      => 'displayfield',
                    'ui'         => 'parameter',
                    'fieldLabel' => '#Name',
                    'value'      => SYMBOL_NONAME
                ];
                $fieldset['items'][] = [
                    'xtype'      => 'displayfield',
                    'ui'         => 'parameter',
                    'fieldLabel' => '#Description',
                    'value'      => SYMBOL_NONAME
                ];
                $fieldset['items'][] = [
                    'xtype' => 'label',
                    'style' => 'color:#e78155;padding-left:45px',
                    'text' => $this->creator->t('will not be added because there are no localization files')
                ];
            }
        }
        return $fieldset;
    }

    /**
     * Возвращает набор полей авторских прав. 
     * 
     * @param array $author Авторские права.
     * 
     * @return array
     */
    protected function getAuthorFieldset(array $author): array
    {
        if ($author['licenseUrl'])
            $license = $author['license'] ? Html::a($author['license'], null, ['target' => '_blank', 'href' => $author['licenseUrl']]) : '';
        else
            $license = $author['license'];
        return [
            'xtype'    => 'fieldset',
            'title'    => '#Literary property',
            'defaults' => [
                'xtype'      => 'displayfield',
                'ui'         => 'parameter',
                'labelAlign' => 'right',
                'labelWidth' => 120
            ],
            'items' => [
                [
                    'fieldLabel' => '#License',
                    'value'      => $license ?: '#[ unknown ]'
                ]
            ]
        ];
    }

    /**
     * Возвращает набор полей с требованиями к устанавливаемой версии модуля. 
     * 
     * @param array $required Требования.
     * @param string $default Значение по умолчанию если требования отсутствуют.
     * 
     * @return array
     */
    protected function getRequiredFieldset(array $required, string $default = ''): array
    {
        $fieldset = [
            'xtype'    => 'fieldset',
            'title'    => '#Requirements',
            'defaults' => [
                'xtype'      => 'displayfield',
                'ui'         => 'parameter',
                'labelAlign' => 'right',
                'labelWidth' => 140
            ],
            'items' => []
        ];

        // если требования отсутствуют
        if (empty($required)) {
            $fieldset['items'][] = [
                'xtype' => 'box',
                'html'  => $default
            ];
            return $fieldset;
        }

        /** @var \Ge\Version\Compare $compare */
        $compare = Ge::$app->version->getCompare();
        /** @var array $result */
        $result = $compare->requirement($required, ' ', ', ', '<span class="required"></span>');
        /** @var array|string $typesLo Локализация видов требований */
        $typesLo = $this->creator->t('{types}');
        // перечень требований
        foreach ($result as $type => $label) {
            $fieldset['items'][] = [
                'fieldLabel' => $typesLo[$type] ?? $type,
                'value'      => $label
            ];
        }
        return $fieldset;
    }

    /**
     * Формирует вкладку "Информация о расширении".
     * 
     * @param array $params Параметры для формирования контента вкладки.
     * 
     * @return array Вкладка (Ext.tab.Panel Sencha ExtJS).
     */
    protected function getTabInformation(array $params): array
    {
        $moduleInfo = null;
        $moduleName = SYMBOL_NONAME;
        $moduleDesc = SYMBOL_NONAME;

        // страница автора
        $authorUrl = $params['version']['authorUrl'];
        if ($authorUrl)
            $authorUrl = Html::a($authorUrl, null, ['target' => '_blank', 'href' => $authorUrl]);
        else
            $authorUrl = '#[ unknown ]';
        // страница модуля
        $versionUrl = $params['version']['url'];
        if ($versionUrl)
            $versionUrl = Html::a($versionUrl, null, ['target' => '_blank', 'href' => $versionUrl]);
        else
            $versionUrl = '#[ unknown ]';
        // если есть уведомление об ошибке в параметрах модуля
        $notice = [];
        if ($this->notice) {
            $notice = [
                'xtype' => 'box',
                'html' => '<div class="notice">' . $this->notice . '</div>'
            ];
        }

        /** @var \Ge\ModuleManager\ModuleManager $modules Менеджер модулей */
        $modules = Ge::$app->modules;
        /** @var null|array $module Модуль расширения */
        $module = $modules->getRegistry()->get($params['install']['moduleId']);
        if ($module) {
            $moduleInfo   = $modules->getInfo($module, ['icon' => true]);
            $localization = $modules->selectName($module['rowId']);
            if ($localization) {
                $moduleName = $localization['name'];
                $moduleDesc = $localization['description'];
            }
        }

        return [
            'title'      => '#Information',
            'cls'        => 'g-plugin_tab-info',
            'autoScroll' => true,
            'items'      => [
                [
                    'xtype' => 'hidden',
                    'name'  => 'installId',
                    'value' => $params['installId']
                ],
                [
                    'xtype' => 'displayfield',
                    'cls'   => 'g-form__display__header',
                    'width' => '100%',
                    'value' => Html::tags([
                        Html::tag('div', $this->creator->t('Information about the extension'), ['class' => 'g-form__display__text']),
                        Html::tag('div', $this->creator->t('version and author of the extension'), ['class' => 'g-form__display__subtext'])
                    ])
                ],
                [
                    'xtype' => 'container',
                    'style' => 'padding:0 5px 5px 5px',
                    'items' => [
                        [
                            'xtype'      => 'displayfield',
                            'ui'         => 'parameter-head',
                            'fieldLabel' => '#Name',
                            'labelAlign' => 'right',
                            'labelWidth' => 93,
                            'style'      => 'margin-top:10px;',
                            'value'      => $params['name']
                        ],
                        [
                            'xtype'      => 'displayfield',
                            'ui'         => 'parameter-head',
                            'fieldLabel' => '#Description',
                            'labelAlign' => 'right',
                            'labelWidth' => 93,
                            'style'      => 'margin-bottom:20px;',
                            'value'      => $params['description']
                        ],
                        [
                            'xtype'    => 'fieldset',
                            'title'    => '#Version',
                            'defaults' => [
                                'labelAlign' => 'right',
                                'anchor'     => '100%',
                                'labelWidth' => 140
                            ],
                            'items' => [
                                [
                                    'xtype'      => 'displayfield',
                                    'ui'         => 'parameter',
                                    'fieldLabel' => '#Date',
                                    'value'      => $params['version']['versionDate'] ? Ge::$app->formatter->toDate($params['version']['versionDate']) : '#[ unknown ]'
                                ],
                                [
                                    'xtype'      => 'displayfield',
                                    'ui'         => 'parameter',
                                    'fieldLabel' => '#Version',
                                    'value'      => $params['version']['version'] ?: '#[ unknown ]'
                                ],
                                [
                                    'xtype'      => 'displayfield',
                                    'ui'         => 'parameter',
                                    'fieldLabel' => '#Extension page',
                                    'value'      => $versionUrl
                                ]
                            ]
                        ],
                        [
                            'xtype'    => 'fieldset',
                            'title'    => '#The extension is for the module',
                            'style'    => $moduleInfo ? 'background: url("' . $moduleInfo['icon'] .'") no-repeat 35px center' : '',
                            'defaults' => [
                                'labelAlign' => 'right',
                                'anchor'     => '100%',
                                'labelWidth' => 140
                            ],
                            'items' => [
                                [
                                    'xtype'      => 'displayfield',
                                    'ui'         => 'parameter',
                                    'fieldLabel' => '#Name',
                                    'value'      => $moduleName
                                ],
                                [
                                    'xtype'      => 'displayfield',
                                    'ui'         => 'parameter',
                                    'fieldLabel' => '#Description',
                                    'value'      => $moduleDesc
                                ]
                            ]
                        ],
                        [
                            'xtype'    => 'fieldset',
                            'title'    => '#Author',
                            'defaults' => [
                                'labelAlign' => 'right',
                                'anchor'     => '100%',
                                'labelWidth' => 110
                            ],
                            'items' => [
                                [
                                    'xtype'      => 'displayfield',
                                    'ui'         => 'parameter',
                                    'fieldLabel' => '#Author name',
                                    'value'      => $params['version']['author'] ?: '#[ unknown ]'
                                ],
                                [
                                    'xtype'      => 'displayfield',
                                    'ui'         => 'parameter',
                                    'fieldLabel' => 'E-mail',
                                    'value'      => $params['version']['email'] ?: '#[ unknown ]'
                                ],
                                [
                                    'xtype'      => 'displayfield',
                                    'ui'         => 'parameter',
                                    'fieldLabel' => '#Author page',
                                    'value'      => $authorUrl
                                ]
                            ]
                        ],
                        $this->getAuthorFieldset($params['version']),
                        $this->getRequiredFieldset($params['install']['required'], $this->creator->t('[ missing ]')),
                        $notice
                    ]
                ]
            ]
        ];
    }

    /**
     * Возвращает вкладку "Конфигурация расширения".
     * 
     * @param array $params Параметры формирования контента вкладки.
     * 
     * @return array Вкладка (Ext.tab.Panel Sencha ExtJS).
     */
    protected function getTabConfiguration(array $params): array
    {
        return [
            'title'      => '#Configuration',
            'cls'        => 'g-plugin_tab-info',
            'autoScroll' => true,
            'items'      => [
                [
                    'xtype' => 'displayfield',
                    'cls'   => 'g-form__display__header',
                    'width' => '100%',
                    'value' => Html::tags([
                        Html::tag('div', $this->creator->t('Extension configuration'), ['class' => 'g-form__display__text']),
                        Html::tag('div', $this->creator->t('extension installation options'), ['class' => 'g-form__display__subtext'])
                    ])
                ],
                [
                    'xtype'    => 'container',
                    'style'    => 'padding:0 5px 5px 5px',
                    'defaults' => [
                        'labelAlign' => 'right',
                        'anchor'     => '100%',
                        'labelWidth' => 110
                    ],
                    'items' => [
                        [
                            'xtype'      => 'displayfield',
                            'ui'         => 'parameter',
                            'fieldLabel' => '#Identifier',
                            'labelWidth' => 116,
                            'value'      => $params['install']['id'],
                        ],
                        [
                            'xtype'      => 'displayfield',
                            'ui'         => 'parameter',
                            'fieldLabel' => '#Path',
                            'labelWidth' => 116,
                            'value'      => $params['install']['path'],
                        ],
                        [
                            'xtype'      => 'displayfield',
                            'ui'         => 'parameter',
                            'fieldLabel' => '#Route',
                            'labelWidth' => 116,
                            'value'      => $params['install']['route'] ?? '#[ unknown ]',
                        ],
                        $this->getNamesFieldset($params),
                        $this->getInterfaceFieldset()
                    ]
                ]
            ]
        ];
    }

    /**
     * Возвращает вкладку "Права доступа".
     * 
     * @param array $params Параметры формирования контента вкладки.
     * 
     * @return null|array Если `null`, права доступа отсутствуют. Иначе, вкладка (Ext.tab.Panel Sencha ExtJS).
     */
    protected function getTabPermissions(array $params): ?array
    {
        if (empty($params['install']['permissions'])) {
            return null;
        }
        return [
            'title'      => '#Permissions',
            'cls'        => 'g-plugin_tab-info',
            'autoScroll' => true,
            'items'      => [
                [
                    'xtype' => 'displayfield',
                    'cls'   => 'g-form__display__header',
                    'width' => '100%',
                    'value' => Html::tags([
                        Html::tag('div', $this->creator->t('Permissions'), ['class' => 'g-form__display__text']),
                        Html::tag('div', $this->creator->t('user access rights to control the extensions'), ['class' => 'g-form__display__subtext'])
                    ])
                ],
                [
                    'xtype' => 'container',
                    'style' => 'padding:0 5px 5px 5px',
                    'items' => [
                        $this->getExtPermissionsFieldset($params['install']),
                        $this->getRolesFieldset($params['install']),
                        [
                            'xtype' => 'label',
                            'ui'    => 'fieldset-comment',
                            'html'  => '#will have access to the extension with access rights - any'
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * Возвращает вкладки окна.
     * 
     * @return array
     */
    protected function renderTabs(): array
    {
        return [
            $this->getTabInformation($this->info),
            $this->getTabConfiguration($this->info),
            $this->getTabPermissions($this->info)
        ];
    }

    /**
     * Возвращает заголовок окна.
     * 
     * @return string
     */
    protected function renderTitle(): string
    {
        if ($this->title) return $this->title;

        return sprintf('%s <span>%s</span>',
            $this->creator->t('{install.title}', [$this->info['name'] ?? '']),
            $this->creator->t('{install.subtitle}')
        );

    }

    /**
     * Возвращает значок окна.
     * 
     * @return string
     */
    protected function renderIcon(): string
    {
        return $this->icon ?: $this->info['icon'] ?? '';
    }

    /**
     * {@inheritdoc}
     */
    public function beforeRender(): bool
    {
        $this->icon = $this->renderIcon();
        $this->title = $this->renderTitle();
        $this->tabs = $this->renderTabs();

        // панель кнопок формы (Ext.form.Panel.buttons Sencha ExtJS)
        $this->form->setStateButtons(
            Form::STATE_CUSTOM,
            $this->notice ? 
            ['help' => ['subject' => 'install'], 'cancel'] :
            [
            'help' => ['subject' => 'install'],
            'action' => [
                'iconCls'     => 'g-icon-svg g-icon_size_14 g-icon-m_save-1',
                'text'        => '#Install',
                'handlerArgs' => ['routeRule' => 'complete'],
            ],
            'cancel'
        ]);
        return true;
    }
}

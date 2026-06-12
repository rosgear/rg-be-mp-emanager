<?php
/**
 * Этот файл является частью расширения модуля веб-приложения RosGear.
 * 
 * Пакет русской локализации.
 * 
 * @link https://rosgear.ru/
 * @copyright Copyright (c) 2015 RosGear
 * @license https://rosgear.ru/license/
 */

return [
    '{name}'        => 'Менеджер расширений модулей',
    '{description}' => 'Управление расширениями модулей системы',
    '{permissions}' => [
        'any'       => ['Полный доступ', 'Просмотр и внесение изменений в расширения модулей системы'],
        'view'      => ['Просмотр', 'Просмотр расширений модулей'],
        'read'      => ['Чтение', 'Чтение расширений модулей'],
        'install'   => ['Установка', 'Установка расширений модулей'],
        'uninstall' => ['Удаление', 'Удаление и демонтаж расширений модулей']
    ],

    // Grid: панель инструментов
    'Edit record' => 'Редактировать',
    'Update' => 'Обновить',
    'Update configurations of installed extensions' => 'Обновление конфигурации установленных расширений модулей',
    'Extension enabled' => 'Доступ к расширению модуля',
    'You need to select a extension' => 'Вам нужно выбрать расширение модуля',
    'Download' => 'Скачать',
    'Downloads module extension package file' => 'Скачивает файла пакета расширения модуля',
    'Uploads module extension package file' => 'Загружает файл пакета расширения модуля',
    // Grid: панель инструментов / Установить (install)
    'Install' => 'Установить',
    'Extension install' => 'Установка расширения модуля',
    // Grid: панель инструментов / Удалить (uninstall)
    'Uninstall' => 'Удалить',
    'Completely delete an installed extension' => 'Полностью удаление установленного расширения модуля',
    'Are you sure you want to completely delete the installed extension?' => 'Вы уверены, что хотите полностью удалить установленное расширение модуля (все файлы модуля будут удалены)?',
    // Grid: панель инструментов / Удалить (delete)
    'Delete' => 'Удалить',
    'Delete an uninstalled extension from the repository' => 'Удаление не установленного расширения модуля из репозитория',
    'Are you sure you want to delete the uninstalled extension from the repository?' => 'Вы уверены, что хотите удалить не установленное расширение модуля из репозитория?',
    // Grid: панель инструментов / Демонтаж (unmount)
    'Unmount' => 'Демонтаж',
    'Delete an installed extension without removing it from the repository' => 'Удаление установленного расширения модуля без удаления его из репозитория',
    'Are you sure you want to remove the installed extension without removing it from the repository?' 
        => 'Вы уверены, что хотите удалить установленное расширения модуля без удаления его из репозитория?',
    // Grid: фильтр
    'All' => 'Все',
    'Installed' => 'Установленные',
    'None installed' => 'Не установленные',
    // Grid: поля
    'Name' => 'Название',
    'Extension id' => 'Идентификатор',
    'Record id' => 'Идентификатор записи',
    'Path' => 'Путь',
    'Enabled' => 'Доступен',
    'Route' => 'Маршурт',
    'Author' => 'Автор',
    'Version' => 'Версия',
    'Module name' => 'Имя модуля',
    'from' => 'от',
    'Description' => 'Описание',
    'Resource' => 'Ресурсы',
    'Date' => 'Дата',
    'Go to extension' => 'Перейти к расширению модуля',
    'Extension settings' => 'Настройка расширения модуля',
    'Extension info' => 'Информация о расширении модуля',
    'For append item menu' => 'Вызов расширения модуля в главном меню панели управления',
    'Status' => 'Статус',
    // Grid: значения
    FRONTEND => 'Сайт',
    BACKEND => 'Панель управления',
    'Yes' => 'да',
    'No' => 'нет',
    'installed' => 'установлен',
    'not installed' => 'не установлен',
    'broken' => 'ошибка',
    'unknow' => 'неизвестно',
    // Grid: всплывающие сообщения / заголовок
    'Disabled' => 'Отключен',
    'Unmounting' => 'Демонтаж',
    'Uninstalling' => 'Удаление',
    'Deleting' => 'Удаление',
    'Downloading' => 'Скачивание',
    // Grid: всплывающие сообщения / текст
    'Extension {0} - enabled' => 'Расширение "<b>{0}</b>" - <b>доступно</b>.',
    'Extension {0} - disabled' => 'Расширение "<b>{0}</b>" - <b>отключено</b>.',
    'Extensions configuration files are updated' => 'Файлы конфигурации расширений модулей обновлены!',
    'Updating extensions' => 'Обновление расширений модулей',
    'Unmounting of extension "{0}" completed successfully' => 'Демонтаж расширения "{0}" успешно завершен.',
    'Uninstalling of extension "{0}" completed successfully' => 'Удаление расширения "{0}" успешно завершено.',
    'Deleting of extension completed successfully' => 'Удаление расширения выполнено успешно.',
    'The module extension package will now be loaded' => 'Сейчас будет выполнена загрузка пакета расширения модуля.',
    // Grid: сообщения (ошибки)
    'There is no extension with the specified id "{0}"' => 'Расширение с указанным идентификатором "{0}" отсутствует',
    'Extension installation configuration file is missing' => 'Отсутствует файл конфигурации установки расширения (.install.php).',
    'It is not possible to remove the extension from the repository because it\'s installed' 
        => 'Невозможно выполнить удаление расширения из репозитория, т.к. оно установлено.',
    // Grid: аудит записей
    'extension {0} with id {1} is enabled' => 'предоставление доступа к расширению "<b>{0}</b>" c идентификатором "<b>{1}</b>"',
    'extension {0} with id {1} is disabled' => 'отключение доступа к расширению "<b>{0}</b>" c идентификатором "<b>{1}</b>"',

    // Form
    '{form.title}' => 'Редактирование расширения "{title}"',
    '{form.subtitle}' => 'Редактирование базовых настроек расширения модуля',
    // Form: поля
    'Identifier' => 'Идентификатор',
    'Record identifier' => 'Идентификатор записи',
    'Default' => 'По умолчанию',
    'enabled' => 'доступен',

    // Upload
    '{upload.title}' => 'Загрузка файла пакета расширения модуля',
    // Upload: панель инструментов
    'Upload' => 'Загрузить',
    // Upload: поля
    'File name' => 'Имя файла',
    '(more details)' => '(подробнее)',
    'The file(s) will be downloaded according to the parameters for downloading resources to the server {0}' 
        => 'Загрузка файла(ов) будет выполнена согласно <em>"параметрам загрузки ресурсов на сервер"</em>. Только расширение файла ".gpk". {0}',
    // Upload: всплывающие сообщения / заголовок
    'Uploading a file' => 'Загрузка файла',
    // Upload: сообщения
    'File uploading error' => 'Ошибка загрузки файла пакета расширения модуля.',
    'Error creating temporary directory to download module extension package file' 
        => 'Ошибка создания временного каталога для загрузки файла пакета расширения модуля.',
    'File uploaded successfully' => 'Файл пакета расширения модуля успешно загружен.',
    'The module extension package file does not contain one of the attributes: id, type' 
        => 'Файл пакета расширения модуля не содержит один из атрибутов: "id" или "type".',
    'Module extension attribute "{0}" is incorrectly specified' => 'Неправильно указан атрибут "{0}" расширения модуля.',
    'You already have the module extension "{0}" installed. Please remove it and try again' 
        => 'У Вас уже установлено расширение модуля "{0}". Удалите его и повторите действие заново.',
    'You already have a module extension with files installed: {0}' 
        => 'У Вас уже установлено расширение модуля со следующими файлами, удалиет их и <br>повторите действие заново: <br><br>{0}<br>...',

    // ShortcodeSettings: сообщения (ошибки)
    'Unable to show extension shortcode settings' => 'Невозможно показать настройки шорткода модуля расширения.',
];

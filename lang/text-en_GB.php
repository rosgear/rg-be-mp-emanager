<?php
/**
 * Этот файл является частью расширения модуля веб-приложения RosGear.
 * 
 * Пакет английской (британской) локализации.
 * 
 * @link https://rosgear.ru/
 * @copyright Copyright (c) 2015 RosGear
 * @license https://rosgear.ru/license/
 */

return [
    '{name}'        => 'Extension Module Manager',
    '{description}' => 'Management of system module extensions',
    '{permissions}' => [
        'any'       => ['Full access', 'View and make changes to system module extensions'],
        'view'      => ['View', 'View module extensions'],
        'read'      => ['Read', 'Read module extensions'],
        'install'   => ['Install', 'Install module extensions'],
        'uninstall' => ['Uninstall', 'Remove and uninstall module extensions']
    ],

    // Grid: панель инструментов
    'Edit record' => 'Edit record',
    'Update' => 'Update',
    'Update configurations of installed extensions' => 'Update configurations of installed extensions',
    'Extension enabled' => 'Extension enabled',
    'You need to select a extension' => 'You need to select a extension',
    'Download' => 'Download',
    'Downloads module extension package file' => 'Downloads module extension package file',
    'Uploads module extension package file' => 'Uploads module extension package file',
    // Grid: панель инструментов / Установить (install)
    'Install' => 'Install',
    'Extension install' => 'Extension install',
    // Grid: панель инструментов / Удалить (uninstall)
    'Uninstall' => 'Uninstall',
    'Completely delete an installed extension' => 'Completely delete an installed extension',
    'Are you sure you want to completely delete the installed extension?' => 'Are you sure you want to completely delete the installed extension?',
    // Grid: панель инструментов / Удалить (delete)
    'Delete' => 'Delete',
    'Delete an uninstalled extension from the repository' => 'Delete an uninstalled extension from the repository',
    'Are you sure you want to delete the uninstalled extension from the repository?' => 'Вы уверены, что хотите удалить не установленное расширение модуля из репозитория?',
    // Grid: панель инструментов / Демонтаж (unmount)
    'Unmount' => 'Unmount',
    'Delete an installed extension without removing it from the repository' => 'Delete an installed extension without removing it from the repository',
    'Are you sure you want to remove the installed extension without removing it from the repository?' 
        => 'Are you sure you want to remove the installed extension without removing it from the repository?',
    // Grid: фильтр
    'All' => 'All',
    'Installed' => 'Installed',
    'None installed' => 'None installed',
    // Grid: поля
    'Name' => 'Name',
    'Extension id' => 'Extension id',
    'Record id' => 'Record id',
    'Path' => 'Path',
    'Enabled' => 'Enabled',
    'Route' => 'Route',
    'Author' => 'Author',
    'Version' => 'Version',
    'Module name' => 'Module name',
    'from' => 'from',
    'Description' => 'Description',
    'Resource' => 'Resource',
    'Date' => 'Date',
    'Go to extension' => 'Go to extension',
    'Extension settings' => 'Extension settings',
    'Extension info' => 'Extension info',
    'For append item menu' => 'Calling the module extension in the main menu of the control panel',
    'Status' => 'Status',
    // Grid: значения
    FRONTEND => 'Site',
    BACKEND => 'Panel control',
    'Yes' => 'yes',
    'No' => 'no',
    'installed' => 'installed',
    'not installed' => 'not installed',
    'broken' => 'broken',
    'unknow' => 'unknow',
    // Grid: всплывающие сообщения / заголовок
    'Disabled' => 'Disabled',
    'Unmounting' => 'Unmounting',
    'Uninstalling' => 'Uninstalling',
    'Deleting' => 'Deleting',
    'Downloading' => 'Downloading',
    // Grid: всплывающие сообщения / текст
    'Extension {0} - enabled' => 'Extension "<b>{0}</b>" - <b>enabled</b>.',
    'Extension {0} - disabled' => 'Extension "<b>{0}</b>" - <b>disabled</b>.',
    'Extensions configuration files are updated' => 'Extensions configuration files are updated!',
    'Updating extensions' => 'Updating extensions',
    'Unmounting of extension "{0}" completed successfully' => 'Unmounting of extension "{0}" completed successfully.',
    'Uninstalling of extension "{0}" completed successfully' => 'Uninstalling of extension "{0}" completed successfully.',
    'Deleting of extension completed successfully' => 'Deleting of extension completed successfully.',
    'The module extension package will now be loaded' => 'The module extension package will now be loaded.',
    // Grid: сообщения (ошибки)
    'There is no extension with the specified id "{0}"' => 'There is no extension with the specified id "{0}"',
    'Extension installation configuration file is missing' => 'Extension installation configuration file is missing (.install.php).',
    'It is not possible to remove the extension from the repository because it\'s installed' 
        => 'It is not possible to remove the extension from the repository because it\'s installed.',
    // Grid: аудит записей
    'extension {0} with id {1} is enabled' => 'extension "<b>{0}</b>" with id "<b>{1}</b>" is enabled',
    'extension {0} with id {1} is disabled' => 'extension "<b>{0}</b>"with id "<b>{1}</b>" is disabled',

    // Form
    '{form.title}' => 'Editing an extension "{title}"',
    '{form.subtitle}' => 'Editing the basic settings of a module extension',
    // Form: поля
    'Identifier' => 'Identifier',
    'Record identifier' => 'Record identifier',
    'Default' => 'Default',
    'enabled' => 'enabled',

    // Upload
    '{upload.title}' => 'Loading module extension package file',
    // Upload: панель инструментов
    'Upload' => 'Upload',
    // Upload: поля
    'File name' => 'File name',
    '(more details)' => '(more details)',
    'The file(s) will be downloaded according to the parameters for downloading resources to the server {0}' 
        => 'The file(s) will be downloaded according to the parameters for downloading resources to the server. File extension only ".gpk". {0}',
    // Upload: всплывающие сообщения / заголовок
    'Uploading a file' => 'Uploading a file',
    // Upload: сообщения
    'File uploading error' => 'Error loading module extension package file.',
    'Error creating temporary directory to download module extension package file' 
        => 'Error creating temporary directory to download module extension package file.',
    'File uploaded successfully' => 'File uploaded successfully.',
    'The module extension package file does not contain one of the attributes: id, type' 
        => 'The module extension package file does not contain one of the attributes: id, type.',
    'Module extension attribute "{0}" is incorrectly specified' => 'Module extension attribute "{0}" is incorrectly specified.',
    'You already have the module extension "{0}" installed. Please remove it and try again' 
        => 'You already have the module extension "{0}" installed. Please remove it and try again.',
    'You already have a module extension with files installed: {0}' 
        => 'You already have a module extension with files installed: <br><br>{0}<br>...',

    // ShortcodeSettings: сообщения (ошибки)
    'Unable to show extension shortcode settings' => 'Unable to show extension shortcode settings.',
];

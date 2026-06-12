<?php
/**
 * Этот файл является частью расширения модуля веб-приложения RosGear.
 * 
 * Файл конфигурации Карты SQL-запросов.
 * 
 * @link https://rosgear.ru/
 * @copyright Copyright (c) 2015 RosGear
 * @license https://rosgear.ru/license/
 */

return [
    'drop'   => ['{{extension}}', '{{extension_locale}}', '{{extension_permissions}}'],
    'create' => [
        '{{extension}}' => function () {
            return "CREATE TABLE `{{extension}}` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `module_id` int(11) unsigned DEFAULT NULL,
                `extension_id` varchar(100) DEFAULT NULL,
                `index` int(11) unsigned DEFAULT '1',
                `namespace` varchar(255) DEFAULT NULL,
                `path` varchar(255) DEFAULT NULL,
                `route` varchar(100) DEFAULT NULL,
                `name` varchar(255) DEFAULT NULL,
                `description` varchar(255) DEFAULT NULL,
                `desk` tinyint(1) unsigned DEFAULT '0',
                `menu` tinyint(1) unsigned DEFAULT '0',
                `enabled` tinyint(1) unsigned DEFAULT '1',
                `has_info` tinyint(1) unsigned DEFAULT '0',
                `has_settings` tinyint(1) unsigned DEFAULT '0',
                `permissions` text,
                `version` varchar(50) DEFAULT '1.0',
                `_updated_date` datetime DEFAULT NULL,
                `_updated_user` int(11) unsigned DEFAULT NULL,
                `_created_date` datetime DEFAULT NULL,
                `_created_user` int(11) unsigned DEFAULT NULL,
                `_lock` tinyint(1) unsigned DEFAULT '0',
                PRIMARY KEY (`id`)
            ) ENGINE={engine} 
            DEFAULT CHARSET={charset} COLLATE {collate}";
        },

        '{{extension_locale}}' => function () {
            return "CREATE TABLE `{{extension_locale}}` (
                `extension_id` int(11) unsigned NOT NULL,
                `language_id` int(11) unsigned NOT NULL,
                `name` varchar(255) DEFAULT NULL,
                `description` varchar(255) DEFAULT '',
                `permissions` text,
                PRIMARY KEY (`extension_id`,`language_id`),
                KEY `language` (`language_id`),
                KEY `module_and_language` (`extension_id`,`language_id`)
            ) ENGINE={engine} 
            DEFAULT CHARSET={charset} COLLATE {collate}";
        },

        '{{extension_permissions}}' => function () {
            return "CREATE TABLE `{{extension_permissions}}` (
                `extension_id` int(11) unsigned NOT NULL,
                `role_id` int(11) unsigned NOT NULL,
                `permissions` text,
                PRIMARY KEY (`role_id`,`extension_id`)
            ) ENGINE={engine} 
            DEFAULT CHARSET={charset} COLLATE {collate}";
        }
    ],

    'run' => [
        'install'   => ['drop', 'create'],
        'uninstall' => ['drop']
    ]
];
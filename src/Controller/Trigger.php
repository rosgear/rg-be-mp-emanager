<?php
/**
 * Этот файл является частью модуля веб-приложения RosGear.
 * 
 * @link https://rosgear.ru/
 * @copyright Copyright (c) 2015 RosGear
 * @license https://rosgear.ru/license/
 */

 namespace Rg\Backend\Marketplace\ExtensionManager\Controller;

use Ge\Panel\Controller\ComboTriggerController;

/**
 * Контроллера выпадающего списка.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Rg\Backend\Marketplace\ExtensionManager\Controller
 */
class Trigger extends ComboTriggerController
{
    /**
     * {@inheritdoc}
     */
    protected array $triggerNames = [
        'extensions' => 'ExtensionCombo'
    ];
}

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
use Ge\Panel\Data\Model\FormModel;
use Ge\Panel\Controller\GridController;
use Rg\Backend\Marketplace\ExtensionManager\Widget\TabGrid;

/**
 * Контроллер вывода сетки установленных и устанавливаемых модулей.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Rg\Backend\Marketplace\ExtensionManager\Controller
 * @since 1.0
 */
class Grid extends GridController
{
    /**
     * {@inheritdoc}
     */
    public function translateAction(mixed $params, ?string $default = null): ?string
    {
        switch ($this->actionName) {
            // изменение записи по указанному идентификатору
            case 'update':
                /** @var FormModel $model */
                $model = $this->lastDataModel;
                if ($model instanceof FormModel) {
                    $event   = $model->getEvents()->getLastEvent(true);
                    $columns = $event['columns'];
                    // если изменение доступности модуля
                    if (isset($columns['enabled'])) {
                        $enabled = (int) $columns['enabled'];
                        return $this->module->t(
                            'extension {0} with id {1} is ' . ($enabled > 0 ? 'enabled' : 'disabled'), [$model->extensionName, $model->getIdentifier()]
                        );
                    }
                }

            default:
                return parent::translateAction($params, $default);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function createWidget(): TabGrid
    {
        return new TabGrid();
    }

   /**
     * {@inheritdoc}
     */
    public function viewAction(): Response
    {
        /** @var Response $response */
        $response = $this->getResponse();

        /** @var TabGrid $widget */
        $widget = $this->getWidget();
        // если была ошибка при формировании виджета
        if ($widget === false) {
            return $response;
        }

        /** @var \Ge\Panel\Data\Model\GridModel $model модель данных*/
        $model = $this->getModel($this->defaultModel);
        if ($model === false) {
            $response
                ->meta->error(Ge::t('app', 'Could not defined data model "{0}"', [$this->defaultModel]));
            return $response;
        }

        // сброс "dropdown" фильтра таблицы
        $store = $this->module->getStorage();
        $store->directFilter = null; 

        $response
            ->setContent($widget->run())
            ->meta
                ->addWidget($widget);
        return $response;
    }
}

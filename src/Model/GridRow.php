<?php
/**
 * Этот файл является частью расширения модуля веб-приложения RosGear.
 * 
 * @link https://rosgear.ru/
 * @copyright Copyright (c) 2015 RosGear
 * @license https://rosgear.ru/license/
 */

namespace Rg\Backend\Marketplace\ExtensionManager\Model;

use Ge;
use Ge\Panel\Data\Model\FormModel;

/**
 * Модель данных профиля записи установленного расширения.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Rg\Backend\Marketplace\ExtensionManager\Model
 * @since 1.0
 */
class GridRow extends FormModel
{
    /**
     * Идентификатор выбранного расширения.
     * 
     * @var int|null
     */
    protected ?string $extensionId;

    /**
     * Имя выбранного расширения.
     * 
     * @var string|null
     */
    public ?string $extensionName;

    /**
     * {@inheritdoc}
     */
    public function getDataManagerConfig(): array
    {
        return [
            'useAudit'   => true,
            'tableName'  => '{{extension}}',
            'primaryKey' => 'id',
            'fields'     => [
                ['id'],
                ['enabled', 'label' => 'Enabled'],
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        parent::init();

        $this
            ->on(self::EVENT_AFTER_SAVE, function ($isInsert, $columns, $result, $message) {
                if ($message['success']) {
                    if (isset($columns['enabled'])) {
                        $enabled = (int) $columns['enabled'];
                        $message['message'] = $this->module->t('Extension {0} - ' . ($enabled > 0 ? 'enabled' : 'disabled'), [$this->extensionName]);
                        $message['title']   = $this->module->t($enabled > 0 ? 'Enabled' : 'Disabled');
                    }
                }
                // всплывающие сообщение
                $this->response()
                    ->meta
                        ->cmdPopupMsg($message['message'], $message['title'], $message['type']);
            });
    }

    /**
     * {@inheritDoc}
     */
    public function afterValidate(bool $isValid): bool
    {
        if ($isValid) {
            /** @var \Ge\Http\Request $request */
            $request  = Ge::$app->request;
            // имя расширения
            $this->extensionName = $request->post('name');
            if (empty($this->extensionName)) {
                $this->setError(Ge::t('app', 'Parameter passed incorrectly "{0}"', ['Name']));
                return false;
            }
            // идентификатор расширения
            $this->extensionId = $request->post('extensionId');
            if (empty($this->extensionId)) {
                $this->setError(Ge::t('app', 'Parameter passed incorrectly "{0}"', ['Extension Id']));
                return false;
            }
            if (!Ge::$app->extensions->getRegistry()->has($this->extensionId)) {
                $this->setError($this->module->t('There is no extension with the specified id "{0}"', [$this->extensionId]));
                return false;
            }
        }
        return $isValid;
    }

    /**
     * {@inheritdoc}
     */
    public function beforeUpdate(array &$columns): void
    {
        /** @var \Ge\ExtensionManager\ExtensionRegistry $installed */
        $installed = Ge::$app->extensions->getRegistry();
        /** @var \Ge\Http\Request $request */
        $request = Ge::$app->request;
        // доступность расширения
        $enabled = $request->post('enabled');
        if ($enabled !== null) {
            $installed->set($this->extensionId, ['enabled' => $enabled], true);
        }
    }
}

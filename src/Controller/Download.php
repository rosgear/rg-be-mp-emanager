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
use Ge\FilePackager\FilePackager;
use Ge\Panel\Controller\BaseController;

/**
 * Контроллер скачивания файла пакета расширения модуля.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Rg\Backend\Marketplace\ExtensionManager\Controller
 * @since 1.0
 */
class Download extends BaseController
{
    /**
     * {@inheritdoc}
     */
    protected string $defaultAction = 'index';

    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            'verb' => [
                'class'    => '\Ge\Filter\VerbFilter',
                'autoInit' => true,
                'actions'  => [
                    ''     => ['POST', 'ajax' => 'GJAX'],
                    'file' => ['GET']
                ]
            ],
            'audit' => [
                'class'    => '\Ge\Panel\Behavior\AuditBehavior',
                'autoInit' => true,
                'allowed'  => '*',
                'enabled'  => $this->enableAudit
            ]
        ];
    }

    /**
     * Действие "index" подготавливает пакет расширения моудля для скачивания.
     * 
     * @return Response
     */
    public function indexAction(): Response
    {
        /** @var Response $response */
        $response = $this->getResponse(Response::FORMAT_JSONG);
        /** @var \Ge\ExtensionManager\ExtensionManager Менеджер расширений модулей */
        $manager = Ge::$app->extensions;

        /** @var null|string $extensionId Идентификатор установленного расширения */
        $extensionId = Ge::$app->request->post('id');
        if (empty($extensionId)) {
            $message = Ge::t('backend', 'Invalid argument "{0}"', ['id']);

            Ge::debug('Error', ['error' => $message]);
            $response
                ->meta->error($message);
            return $response;
        }

        /** @var null|array $params Параметры установленного расширения модуля */
        $params = $manager->getRegistry()->get($extensionId);
        // модуль с указанным идентификатором не установлен
        if ($params === null) {
            $message = $this->module->t('There is no module extension with the specified id "{0}"', [$extensionId]);

            Ge::debug('Error', ['error' => $message]);
            $response
                ->meta->error($message);
            return $response;
        }

        /** @var null|array $version Параметры установленного расширения модуля */
        $version = $manager->getVersion($extensionId);
        // модуль с указанным идентификатором не установлен
        if ($version === null) {
            $message = $this->module->t('There is no module extension with the specified id "{0}"', [$extensionId]);

            Ge::debug('Error', ['error' => $message]);
            $response
                ->meta->error($message);
            return $response;
        }

        /** @var string $packageName Название файла пакета */
        $packageName = FilePackager::generateFilename($extensionId, $version['version']);
        /** @var FilePackager Файл пакета  */
        $packager = new FilePackager([
            'filename' => Ge::alias('@runtime') . DS . $packageName,
        ]);

        /** @var \Ge\FilePackager\Package $package Пакет */
        $package = $packager->getPackage([
            'format' => 'json',
            'path'   => Ge::alias('@runtime')
        ]);
        $package->id     = $extensionId;
        $package->type   = 'extension';
        $package->author = $version['author'];
        $package->date   = $version['versionDate'];
        $package->name   = 'Module extension "' . $version['name'] . '" v' . $version['version'];
        $package->note   = $version['description'];

        // добавление файлов в пакет
        $package->addFiles(Ge::getAlias('@module' . $params['path']), '@module' . $params['path']);

        // проверка и сохранение файла пакета
        if (!$package->save(true)) {
            $message = $package->getError();

            Ge::debug('Error', ['error' => $message]);
            $response
                ->meta->error($message);
            return $response;
        }

        // архивация пакета
        if (!$packager->pack($package)) {
            $message = $package->getError();

            Ge::debug('Error', ['error' => $message]);
            $response
                ->meta->error($message);
            return $response;
        }

        $response
            ->meta
                // всплывающие сообщение
                ->cmdPopupMsg($this->t('The module extension package will now be loaded'), $this->t('Downloading'), 'success')
                // загрузка файла
                ->cmdGe('download', ['@backend/marketplace/emanager/download/file/' . $params['rowId']]);
        return $response;
    }

    /**
     * Действие "file" скачивает файл расширения пакета модуля.
     * 
     * @return Response
     */
    public function fileAction(): Response
    {
        /** @var Response $response */
        $response = $this->getResponse(Response::FORMAT_RAW);
        /** @var \Ge\ExtensionManager\ExtensionManager Менеджер расширения модулей */
        $manager = Ge::$app->extensions;

        /** @var null|int $extensionId Идентификатор установленного модуля */
        $extensionId = (int) Ge::$app->router->get('id');
        if (empty($extensionId)) {
            $message = Ge::t('backend', 'Invalid argument "{0}"', ['id']);

            Ge::debug('Error', ['error' => $message]);
            return $response->setContent($message);
        }

        /** @var null|array $params Параметры установленного расширения модуля */
        $params = $manager->getRegistry()->getAt($extensionId);
        // расширение модуля с указанным идентификатором не установлено
        if ($params === null) {
            $message = $this->module->t('There is no module extension with the specified id "{0}"', [$extensionId]);

            Ge::debug('Error', ['error' => $message]);
            return $response->setContent($message);
        }

        /** @var null|array $version Параметры установленного расширения модуля */
        $version = $manager->getVersion($params['id']);
        // расширение модуля с указанным идентификатором не установлено
        if ($version === null) {
            $message = $this->module->t('There is no module extension with the specified id "{0}"', [$params['id']]);

            Ge::debug('Error', ['error' => $message]);
            return $response->setContent($message);
        }

        /** @var string $packageName Название файла пакета */
        $filename = Ge::alias('@runtime') . DS . FilePackager::generateFilename($params['id'], $version['version']);
        if (!file_exists($filename)) {
            $message = Ge::t('app', 'File "{0}" not found', [$filename]);

            Ge::debug('Error', ['error' => $message]);
            return $response->setContent($message);
        }

        $response->sendFile($filename);
        return $response;
    }
}

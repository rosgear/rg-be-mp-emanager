/*!
 * Панель инструментов.
 * Расширение "Менеджер расширений модулей".
 * Модуль "Маркетплейс".
 * Copyright 2015 RosGear. Anton Tivonenko <anton.tivonenko@gmail.com>
 * https://rosgear.ru/license/
 */

/**
 * @class Rg.be.mp.emanager.ButtonInstall
 * @extends Ge.view.grid.button.Button
 * Кнопка "Установить" на панели инструментов сетки.
 * Установка расширения модуля.
 */
Ext.define('Rg.be.mp.emanager.ButtonInstall', {
    extend: 'Ge.view.grid.button.Button',
    xtype: 'rg-mp-emanager-button-install',

    selectRecords: true,
    minWidth: 76,
    confirm: false,
    disabled: true,

    /**
     * Обработчик событий кнопки.
     * @cfg {Object}
     */
    listeners: {
        /**
         * @event afterrender
         * Событие после рендера компонента.
         * @param {Ge.view.grid.button.Button} me
         * @param {Object} eOpts Параметры слушателя.
         */
        afterrender: function (me, eOpts) {
            me.selectorCmp.getSelectionModel().on('selectionchange', function (sm, selectedRecord) {
                if (Ext.isDefined(selectedRecord[0]))
                    me.setDisabled(selectedRecord[0].data.status != 0);
                else
                    me.setDisabled(true);
            });
        },
        /**
         * @event click
         * Событие клика на кнопке.
         * @param {Ge.view.grid.button.Button} me
         * @param {Event} e
         * @param {Object} eOpts Параметры слушателя.
         */
        click: function (me, e, eOpts) {
            let row = me.selectorCmp.getStore().getOneSelected();
            // row.install = 'path,namespace'
            Ge.app.widget.load('@backend/marketplace/emanager/install/view', {installId: row.installId});
        }
    }
});


/**
 * @class Rg.be.mp.emanager.ButtonInstall
 * @extends Ge.view.grid.button.Button
 * Кнопка "Удаление" на панели инструментов сетки.
 * Полность удаление установленного расширения модуля.
 */
 Ext.define('Rg.be.mp.emanager.ButtonUninstall', {
    extend: 'Ge.view.grid.button.Button',
    xtype: 'rg-mp-emanager-button-uninstall',

    selectRecords: true,
    minWidth: 72,
    confirm: true,
    disabled: true,

    /**
     * Обработчик событий кнопки.
     * @cfg {Object}
     */
    listeners: {
        /**
         * @event afterrender
         * Событие после рендера компонента.
         * @param {Ge.view.grid.button.Button} me
         * @param {Object} eOpts Параметры слушателя.
         */
        afterrender: function (me, eOpts) {
            me.selectorCmp.getSelectionModel().on('selectionchange', function (sm, selectedRecord) {
                let row = selectedRecord[0];
                // status = 1 (установлен), 2 (ошибка), 0 (не установлен)
                // row.data.lock - модуль системный
                if (Ext.isDefined(row)) {
                    me.setDisabled(row.data.status == 0 || row.data.lockRow == 1 || row.data.lock == 1);
                } else
                    me.setDisabled(true);
            });
        }
    }
});


/**
 * @class Rg.be.mp.emanager.ButtonUnmount
 * @extends Ge.view.grid.button.Button
 * Кнопка "Демонтаж" на панели инструментов сетки.
 * Удаление установленного расширения модуля без удаления его из репозитория.
 */
 Ext.define('Rg.be.mp.emanager.ButtonUnmount', {
    extend: 'Ge.view.grid.button.Button',
    xtype: 'rg-mp-emanager-button-unmount',

    selectRecords: true,
    minWidth: 72,
    confirm: true,
    disabled: true,

    /**
     * Обработчик событий кнопки.
     * @cfg {Object}
     */
    listeners: {
        /**
         * @event afterrender
         * Событие после рендера компонента.
         * @param {Ge.view.grid.button.Button} me
         * @param {Object} eOpts Параметры слушателя.
         */
        afterrender: function (me, eOpts) {
            me.selectorCmp.getSelectionModel().on('selectionchange', function (sm, selectedRecord) {
                let row = selectedRecord[0];
                // status = 1 (установлен), 2 (ошибка), 0 (не установлен)
                // row.data.lock - модуль системный
                if (Ext.isDefined(row)) {
                    me.setDisabled(row.data.status == 0 || row.data.lockRow == 1 || row.data.lock == 1);
                } else
                    me.setDisabled(true);
            });
        }
    }
});


/**
 * @class Rg.be.mp.emanager.ButtonDelete
 * @extends Ge.view.grid.button.Button
 * Кнопка "Удалить" на панели инструментов сетки.
 * Удаление не установленного расширения модуля из репозитория.
 */
 Ext.define('Rg.be.mp.emanager.ButtonDelete', {
    extend: 'Ge.view.grid.button.Button',
    xtype: 'rg-mp-emanager-button-delete',

    selectRecords: true,
    minWidth: 72,
    confirm: true,
    disabled: true,

    /**
     * Обработчик событий кнопки.
     * @cfg {Object}
     */
    listeners: {
        /**
         * @event afterrender
         * Событие после рендера компонента.
         * @param {Ge.view.grid.button.Button} me
         * @param {Object} eOpts Параметры слушателя.
         */
        afterrender: function (me, eOpts) {
            me.selectorCmp.getSelectionModel().on('selectionchange', function (sm, selectedRecord) {
                let row = selectedRecord[0];
                // status = 1 (установлен), 2 (ошибка), 0 (не установлен)
                if (Ext.isDefined(row)) {
                    me.setDisabled(row.data.status != 0);
                } else
                    me.setDisabled(true);
            });
        },
        /**
         * @event click
         * 
         * Событие клика на кнопке.
         * @param {Ge.view.grid.button.Button} me
         * @param {Event} e
         * @param {Object} eOpts Параметры слушателя.
         */
         click: function (me, e, eOpts) {
            let row = me.selectorCmp.getStore().getOneSelected();
            // row.install = 'path,namespace'
            Ge.app.widget.load('@backend/marketplace/emanager/extension/delete', {installId: row.installId});
        }
    }
});


/**
 * @class Rg.be.mp.emanager.ButtonDownload
 * @extends Ge.view.grid.button.Button
 * Кнопка "Скачать" на панели инструментов сетки.
 * Скачивание файла пакета расширения модуля.
 */
 Ext.define('Rg.be.mp.emanager.ButtonDownload', {
    extend: 'Ge.view.grid.button.Button',
    xtype: 'rg-mp-emanager-button-download',

    selectRecords: true,
    minWidth: 72,
    confirm: true,
    disabled: true,

    /**
     * Обработчик событий кнопки.
     * @cfg {Object}
     */
    listeners: {
        /**
         * @event afterrender
         * Событие после рендера компонента.
         * @param {Ge.view.grid.button.Button} me
         * @param {Object} eOpts Параметры слушателя.
         */
        afterrender: function (me, eOpts) {
            me.selectorCmp.getSelectionModel().on('selectionchange', function (sm, selectedRecord) {
                let row = selectedRecord[0];
                // status = 1 (установлен), 2 (ошибка), 0 (не установлен)
                if (Ext.isDefined(row)) {
                    me.setDisabled(row.data.status != 1);
                } else
                    me.setDisabled(true);
            });
        },
        /**
         * @event click
         * Событие клика на кнопке.
         * @param {Ge.view.grid.button.Button} me
         * @param {Event} e
         * @param {Object} eOpts Параметры слушателя.
         */
        click: function (me, e, eOpts) {
            let row = me.selectorCmp.getStore().getOneSelected();
            Ge.makeRequest({
                route: '@backend/marketplace/emanager/download',
                params: { id: row.extensionId }
            });
        }
    }
});
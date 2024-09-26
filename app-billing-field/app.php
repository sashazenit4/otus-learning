<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="//cdn.webix.com/edge/webix.css" type="text/css">
    <script src="//cdn.webix.com/edge/webix.js" type="text/javascript"></script>
    <script src="//api.bitrix24.com/api/v1/"></script>
</head>
<body>
<style>
    body {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100vh;
        margin: 0;
        font-family: Arial, sans-serif;
    }

    #preloader {
        width: 50px;
        height: 50px;
        border: 5px solid #000;
        border-top: 5px solid #fff;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 1000;
    }

    .hidden {
        display: none;
    }

    .block-pointers {
        pointer-events: none;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .grid-container {
        position: absolute;
        top: 0;
        margin-top: 15px;
    }
</style>
<?php
$placement = $_REQUEST['PLACEMENT'];
$placementOptions = isset($_REQUEST['PLACEMENT_OPTIONS']) ? json_decode($_REQUEST['PLACEMENT_OPTIONS'], true) : array();

if (empty($placementOptions['ENTITY_VALUE_ID'])) {
    echo 'Для работы с полем создайте карточку';
    die();
}

if (!is_array($placementOptions)) {
    $placementOptions = array();
}

if ($placement === 'DEFAULT') {
    $placementOptions['MODE'] = 'edit';
}
?>

<script>
    async function setValues() {
        const obj = {
            'payments_entity_type_id': null,
            'acts_entity_type_id': null,
            'invoices_entity_type_id': null,
            'payments_entity_id': null,
            'acts_entity_id': null,
            'billing_entity_type_id': null,
        };

        async function getOption(optionName) {
            return new Promise((resolve, reject) => {
                BX24.callMethod('app.option.get', { 'options': optionName }, function(result) {
                    if (result.error()) {
                        reject(result.error());
                    } else {
                        resolve(result.data()[optionName]);
                    }
                });
            });
        }

        for (const key of Object.keys(obj)) {
            if (obj[key] === null) {
                try {
                    obj[key] = await getOption(key);
                } catch (error) {
                    console.error(`Failed to get option ${key}:`, error);
                }
            }
        }

        return obj;
    }

    function togglePreloader() {
        const preloader = document.getElementById('preloader')
        let table = document.getElementById('BILLING_CONTAINER')
        if (preloader.classList.contains('hidden')) {
            preloader.classList.remove('hidden')
            table.classList.add('block-pointers')
        } else {
            preloader.classList.add('hidden')
            table.classList.remove('block-pointers')
        }
    }

    function extractFirstNumber(str) {
        const regex = /\d+/
        const match = str.match(regex)
        return match ? Number(match[0]) : null
    }

    function setupEntityTypeId(entityId, objectEntityIds) {
        return new Promise((resolve, reject) => {
            BX24.callMethod('crm.type.get', {id: entityId}, function (response) {
                if (response.error()) {
                    reject(response.error())
                } else {
                    resolve(response.answer.result.type.entityTypeId)
                }
            })
        }).catch(error => {
            console.error('Ошибка при получении типа:', error)
            let entityTypeIds = {
                [objectEntityIds['payments_entity_id']]: objectEntityIds['payments_entity_type_id'],
                [objectEntityIds['acts_entity_id']]: objectEntityIds['acts_entity_type_id'],
            }
            return entityTypeIds[entityId] ?? objectEntityIds['invoices_entity_type_id']
        })
    }

    function setupColumns(entityTypeId, objectEntityIds) {
        let fieldMap = {
            [objectEntityIds['payments_entity_type_id']]: ['UF_CRM_11_PAYMENT', 'ufCrm11Payment', 'ufCrm12Billing'],
            [objectEntityIds['acts_entity_type_id']]: ['UF_CRM_11_ACT', 'ufCrm11Act', 'ufCrm3Billing'],
            [objectEntityIds['invoices_entity_type_id']]: ['UF_CRM_11_INVOICE', 'ufCrm11Invoice', 'ufCrmSmartInvoiceBilling']
        }

        return fieldMap[entityTypeId] || fieldMap[objectEntityIds['invoices_entity_type_id']]
    }

    function fetchServiceNames(servicesFilter) {
        return new Promise((resolve, reject) => {
            BX24.callMethod('lists.element.get', {
                IBLOCK_TYPE_ID: 'bitrix_processes',
                IBLOCK_CODE: 'servicesList',
                FILTER: servicesFilter,
            }, function (response) {
                if (response.error()) {
                    reject(response.error())
                } else {
                    resolve(response.answer.result)
                }
            })
        })
    }

    function addLeadingZero(value) {
        return String(value).padStart(2, '0');
    }

    function formatDate(isoDate) {
        const date = new Date(isoDate);
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0'); // Месяцы начинаются с 0
        const year = date.getFullYear();

        return `${day}.${month}.${year}`;
    }

    async function createRows(data, billingEntityRelationField) {
        let servicesFilter = {'ID': data.items.map(item => item.ufCrm11Uslugi)}
        let servicesNames = {}
        let elements = await fetchServiceNames(servicesFilter)

        elements.forEach(service => {
            servicesNames[service.ID] = service.NAME
        })

        return data.items.map((row, index) => ({
            id: index + 1,
            checkbox: row[billingEntityRelationField] > 0 ? 1 : 0,
            service: servicesNames[row.ufCrm11Uslugi] ?? '',
            date: row.begindate === null ? '' : formatDate(row.begindate),
            hours: (
                addLeadingZero(parseInt(row.ufCrm11TimeHours ?? 0) || 0) + ':' +
                addLeadingZero((parseInt(row.ufCrm11TimeMinutes ?? 0) || 0))
            ),
            opportunity: (row.opportunity ?? 0).toLocaleString('ru-RU'),
            value: row.id ?? 0
        }))
    }

    function timeSort(a, b) {
        let [hoursA, minutesA] = a.hours.split(":").map(Number);
        let [hoursB, minutesB] = b.hours.split(":").map(Number);

        if (hoursA !== hoursB) {
            return hoursA - hoursB;
        }

        return minutesA - minutesB;
    }

    function setupColumnsDefinitions() {
        return [
            {
                id: "checkbox",
                header: "",
                width: 50,
                template: "{common.checkbox()}"
            },
            {
                id: "service",
                header: "Услуга",
                width: 230,
                sort: 'string',
            },
            {
                id: "date",
                header: "Дата",
                width: 105,
                sort: 'string',
            },
            {
                id: "hours",
                header: "Часы",
                width: 75,
                sort: timeSort
            },
            {
                id: "opportunity",
                header: "Сумма",
                width: 90,
                sort: (a, b) => parseFloat(a.opportunity.replace(',', '.')) - parseFloat(b.opportunity.replace(',', '.'))
            }
        ]
    }

    function setupEvents(entityTypeId, placementOptions, entityBillingField, billingEntityRelationField, objectEntityIds) {
        return {
            onAfterLoad: function () {
                webix.delay(() => {
                    this.adjustRowHeight("service", true)
                    this.render()
                }, this)
            },
            onColumnResize: function () {
                this.adjustRowHeight("service", true)
                this.render()
            },
            onCheck: function (rowId, colId, state) {
                togglePreloader()
                let billingId = this.getItem(rowId).value
                let queries = {}
                let updateField = state === 1 ? placementOptions.ENTITY_VALUE_ID : 0
                queries['query_0'] = ['crm.item.update', {entityTypeId: objectEntityIds['billing_entity_type_id'], id: billingId, fields: {[billingEntityRelationField]: updateField}}]

                let billingIds = []
                this.eachRow(function (row) {
                    let item = this.getItem(row)
                    if (item.checkbox) {
                        billingIds.push(item.value)
                    }
                })
                queries['query_1'] = ['crm.item.update', {entityTypeId, id: placementOptions.ENTITY_VALUE_ID, fields: {[entityBillingField]: billingIds}}]
                BX24.callBatch(queries, function (results) {
                    togglePreloader()
                })
            }
        }
    }

    async function initialize() {
        let placementOptions = <?=json_encode($placementOptions)?>;
        let entityId = extractFirstNumber(placementOptions.ENTITY_ID)
        let entityTypeId = await setupEntityTypeId(entityId, objectEntityIds)
        let [sortBillingEntityRelationField, billingEntityRelationField, entityBillingField] = setupColumns(entityTypeId, objectEntityIds)
        let columns = setupColumnsDefinitions()

        let services = {
            data: [],
            dataIndex: 0,
            services: {},
        }

        BX24.init(() => {
            BX24.callMethod('crm.item.get', {
                entityTypeId: entityTypeId,
                id: placementOptions.ENTITY_VALUE_ID,
            }, async function (result) {
                let dealId = result.answer.result.item.parentId2
                BX24.callMethod('crm.item.list', {
                    entityTypeId: objectEntityIds['billing_entity_type_id'],
                    order: {[sortBillingEntityRelationField]: "DESC"},
                    filter: {
                        "logic": "OR",
                        "0": {
                            [billingEntityRelationField]: placementOptions.ENTITY_VALUE_ID,
                            parentId2: dealId,
                        },
                        "1": {
                            [billingEntityRelationField]: '',
                            parentId2: dealId,
                        },
                    },
                    select: ["id", "title", "opportunity", "begindate", "uf*"]
                }, async function (result) {
                    if (result.error()) {
                        console.error(result.error())
                    } else {
                        let data = result.data()
                        services.data = await createRows(data, billingEntityRelationField)

                        setTimeout(() => {
                            togglePreloader()
                            webix.ready(() => {
                                if (services.data.length <= 0) {
                                    document.body.innerHTML += 'Нет биллингов'
                                } else {
                                    let [webixWidth, webixHeight] = (function () {
                                        let webixWidth, webixHeight;
                                        let windowWidth = window.innerWidth;

                                        webixWidth = 0.95 * windowWidth;
                                        webixHeight = 550;
                                        return [webixWidth, webixHeight]
                                    })();

                                    webix.ui({
                                        container: "BILLING_CONTAINER",
                                        view: "datatable",
                                        autoConfig: true,
                                        scroll: 'xy',
                                        autoheight: false,
                                        autowidth: false,
                                        height: webixHeight,
                                        width: webixWidth,
                                        columns,
                                        fixedRowHeight: false,
                                        rowLineHeight: 25,
                                        resizeColumn: true,
                                        rowHeight: 25,
                                        data: services.data,
                                        on: setupEvents(entityTypeId, placementOptions, entityBillingField, billingEntityRelationField, objectEntityIds),
                                    })
                                }
                            })
                        }, result.more() ? 5000 : 2000)
                    }
                })
            })
        })
    }

    let objectEntityIds = {}

    setValues().then(updatedObj => {
        objectEntityIds = updatedObj
        initialize()
    }).catch(error => {
        objectEntityIds = {
            'payments_entity_type_id': 183,
            'acts_entity_type_id': 157,
            'invoices_entity_type_id': 31,
            'payments_entity_id': 12,
            'acts_entity_id': 3,
            'billing_entity_type_id': 152,
        }
        initialize()
    })
</script>
<div id="preloader"></div>
<div id="BILLING_CONTAINER" class="grid-container"></div>
</body>
</html>

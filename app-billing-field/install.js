let divStatus = document.querySelector('#status');
if (!BX24) {
    divStatus.innerHTML = 'Библиотека Bitrix24 не найдена!';
} else {
    divStatus.innerHTML += 'Приложение "Учтенные услуги" устанавливается...';
    BX24.init(async function () {
        await new Promise((resolve) => {
            BX24.callMethod('userfieldtype.delete', {
                    'USER_TYPE_ID': 'billing_list',
                },
                function (res) {
                    resolve(res);
                });
        })
            .then(async (value) => {
                await new Promise((resolve) => {
                    BX24.callMethod('userfieldtype.add', {
                            'USER_TYPE_ID': 'billing_list',
                            'HANDLER': 'https://' + window.location.host + '/app-billing-field/app.php',
                            'TITLE': 'Учтенные услуги',
                            'DESCRIPTION': 'Поле учтенные услуги',
                            'OPTIONS': {
                                'height': 600,
                            }
                        },
                        function (res) {
                            resolve(res);
                            BX24.callMethod('crm.type.list', {}, function(result) {
                                let payments_entity_type_id = 0
                                let acts_entity_type_id = 0
                                let invoices_entity_type_id = 0
                                let payments_entity_id = 0
                                let acts_entity_id = 0
                                let billing_entity_type_id = 0
                                for (let item of result.data().types) {
                                    if (item.title === 'Биллинг') {
                                        billing_entity_type_id = item.entityTypeId
                                    }
                                    if (item.title === 'Плановые платежи') {
                                        payments_entity_type_id = item.entityTypeId
                                        payments_entity_id = item.id
                                    }
                                    if (item.title === 'Акт(ы)') {
                                        acts_entity_type_id = item.entityTypeId
                                        acts_entity_id = item.id
                                    }
                                    if (item.title === 'Счета') {
                                        invoices_entity_type_id = item.entityTypeId
                                    }
                                }
                                invoices_entity_type_id = invoices_entity_type_id === 0 ? 31 : invoices_entity_type_id
                                BX24.callMethod('app.option.set', {
                                    'options': {
                                        'payments_entity_type_id': payments_entity_type_id,
                                        'acts_entity_type_id': acts_entity_type_id,
                                        'invoices_entity_type_id': invoices_entity_type_id,
                                        'payments_entity_id': payments_entity_id,
                                        'acts_entity_id': acts_entity_id,
                                        'billing_entity_type_id': billing_entity_type_id,
                                    },
                                })
                            })

                        });
                })
                    .then((value) => {
                        BX24.callMethod('placement.get', {},
                            function (res) {
                                BX24.installFinish();
                                divStatus.innerHTML += 'Установка приложения "Учтенные услуги" завершена.';
                            });
                    });
            });
    });
}

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>CRM Button</title>
    <style>
        #blacklistButton {
            background-color: #ff0000;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            font-size: 14px;
            margin: auto;
            display: flex;
        }
    </style>
    <script src="//api.bitrix24.com/api/v1/"></script>
</head>
<body>
<button id="blacklistButton">Перенести в блэклист и пометить некачественным</button>
<script>
    let placementOptions = JSON.parse(<?=json_encode($_REQUEST['PLACEMENT_OPTIONS'])?>);

    document.getElementById('blacklistButton').addEventListener('click', async function () {
        const leadId = placementOptions['ENTITY_VALUE_ID'];
        const phoneNumber = await getLeadPhoneNumber(leadId);
        const badQualityStatusId = 'JUNK';

        addToBlacklist(phoneNumber)
        return updateLeadStatus(leadId, badQualityStatusId);
    });

    BX24.init(function () {
    });

    async function getLeadPhoneNumber(leadId) {
        try {
            const params = {
                id: leadId
            };
            const result = await new Promise((resolve, reject) => {
                BX24.callMethod('crm.lead.get', params, function (result) {
                    if (result.error()) {
                        reject(new Error(result.error().MESSAGE));
                    } else {
                        resolve(result.data());
                    }
                });
            });

            if (result.PHONE && result.PHONE.length > 0) {

                return result.PHONE[0].VALUE;
            } else {
                throw new Error('Номер телефона не найден для данного лида');
            }
        } catch (error) {
            console.error('Ошибка:', error);
            throw error;
        }
    }

    function addToBlacklist(phoneNumber) {
        phoneNumber = phoneNumber.slice(2)
        navigator.clipboard.writeText(phoneNumber).then(function () {
            alert('Номер телефона скопирован в буфер обмена');
            window.open('https://my.calltouch.ru/accounts/34842/sites/51949/settings/phones-list', '_blank');
        }).catch(function (err) {
            console.error('Не удалось скопировать текст: ', err);
        });
    }

    function updateLeadStatus(leadId, statusId) {
        const params = {
            id: leadId,
            fields: {
                STATUS_ID: statusId
            }
        };

        return new Promise((resolve, reject) => {
            BX24.callMethod('crm.lead.update', params, function (result) {
                if (result.error()) {
                    reject(new Error(result.error().MESSAGE));
                } else {
                    resolve(result.data());
                }
            });
        });
    }
</script>
</body>
</html>

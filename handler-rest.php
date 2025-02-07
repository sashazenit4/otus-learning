<?php
file_put_contents($_SERVER['DOCUMENT_ROOT'].'/rest_log.log', var_export($_REQUEST, true), FILE_APPEND);


class DealImporter
{
    public static function createDeal(): void
    {
        $dealFields = [
            'TITLE' => 'Сделка Тестовая',
            'STAGE_ID' => 'PREPARATION',
            'CATEGORY_ID' => 0,
            'OPENED' => 'Y',
        ];

        $response = self::sendRequest('/crm.deal.add', [
            'fields' => $dealFields,
            'params' => ['REGISTER_SONET_EVENT' => 'Y'],
        ]);
    }

    private static function sendRequest(string $endpoint, array $data): array
    {
        $url = 'https://b-coding.bitrix24.ru/rest/1/63mye6yz7kl824l9' . $endpoint;
        $query = http_build_query($data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url . '?' . $query);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded',
        ]);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);

        if ($response === false) {
            $error = curl_error($ch);
            file_put_contents($_SERVER['DOCUMENT_ROOT'].'/rest_log.log', var_export('ОШИБКА: ' . $error, true), FILE_APPEND);
            curl_close($ch);
        }

        curl_close($ch);

        return json_decode($response, true);
    }
}

DealImporter::createDeal();

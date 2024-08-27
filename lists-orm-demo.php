<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

/**
 * @var CMain $APPLICATION
 */

$APPLICATION->SetTitle('Врачи');

use Bitrix\Main\Grid\Options;
use Otus\Models\Lists\DoctorsPropertyValuesTable as DoctorsTable;
use Otus\Models\Lists\ProceduresPropertyValuesTable as ProceduresTable;
use Bitrix\Main\Application;

$context = Application::getInstance()->getContext();
$request = $context->getRequest();

$gridId = 'doctors_grid_id';

$procedures = ProceduresTable::query()
    ->setSelect([
        'ID' => 'ELEMENT.ID',
        'NAME' => 'ELEMENT.NAME',
        'COLOR'
    ])
    ->fetchAll();

if (empty($procedures)) {
    $procedures = [];
}

$proceduresFilter = array_reduce($procedures, function ($acc, $procedure) {
    $acc[$procedure['ID']] = $procedure['NAME'];
    return $acc;
}, []);

if ($request->isPost() && check_bitrix_sessid()) {
    if ($request->getPost('TYPE') == 'DELETE') {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();
        $doctorId = intval($request->getPost('ID'));

        $success = true;

        DoctorsTable::delete($doctorId);

        $response = [
            'success' => $success,
            'doctorId' => $doctorId
        ];

        echo Bitrix\Main\Web\Json::encode($response);
        die();
    }

    if ($request->getPost('apply')) {
        switch ($request->getPost('TYPE')) {
            case 'DOCTOR':
                $doctorId = intval($request->getPost('ID'));
                $proceduresPost = $request->getPost('PROCEDURES');

                if ($doctorId > 0) {
                    DoctorsTable::update($doctorId, [
                        'NAME' => $request->getPost('NAME'),
                        'PROPERTY_VALUES' => [
                            DoctorsTable::getPropertyId('PROCEDURES_ID') => $proceduresPost
                        ]
                    ]);

                    $doctorPostId = $doctorId;
                } else {
                    $doctorPostId = DoctorsTable::add([
                        'NAME' => $request->getPost('NAME'),
                        DoctorsTable::getPropertyId('PROCEDURES_ID') => $proceduresPost
                    ]);
                }

                break;

            case 'PROCEDURES':
                $procedureId = intval($request->getPost('ID'));
                $colors = $request->getPost('COLORS');

                if ($procedureId > 0) {
                    ProceduresTable::update($procedureId, [
                        'NAME' => $request->getPost('NAME'),
                        'PROPERTY_VALUES' => [
                            ProceduresTable::getPropertyId('COLOR') => $colors
                        ]
                    ]);
                } else {
                    $procedureId = ProceduresTable::add([
                        'NAME' => $request->getPost('NAME'),
                        ProceduresTable::getPropertyId('COLOR') => $colors
                    ]);
                }

                break;
        }
    }
}

if ($request['IFRAME'] == 'Y' && $request['TYPE'] == 'DOCTOR') {
    global $APPLICATION;
    $APPLICATION->RestartBuffer();
    $APPLICATION->SetTitle('Добавить врача');

    $doctorId = intval($request['id']);

    if ($doctorPostId) {
        $doctorId = $doctorPostId;
        $APPLICATION->SetTitle('Обновить данные врача');
    }

    $doctor = DoctorsTable::query()
        ->setSelect([
            'ID' => 'ELEMENT.ID',
            'NAME' => 'ELEMENT.NAME',
            'PROCEDURES'
        ])
        ->setFilter([
            'ID' => $doctorId
        ])
        ->fetch();

    $defaultValues = [];
    if ($doctor['PROCEDURES']) {
        $defaultValues = array_keys($doctor['PROCEDURES']);
    }

    $proceduresSelect = array_map(function ($procedure) use ($defaultValues) {
        $selected = in_array($procedure['ID'], $defaultValues) ? ' selected' : '';
        return "<option value='{$procedure['ID']}'{$selected}>{$procedure['NAME']}</option>";
    }, $procedures);
    $proceduresSelectHtml = '<select name="PROCEDURES[]" multiple="multiple">
                                ' . implode('', $proceduresSelect) . '
                            </select>';

    $tabs = [
        [
            "id" => "tab1",
            "name" => $doctorPostId ? "Редактировать данные врача" : "Добавить врача",
            "title" => $doctor['NAME'],
            "fields" => [
                [
                    "id" => "NAME",
                    "name" => "Имя",
                    "type" => "text",
                    "value" => $doctor['NAME']
                ],
                [
                    "id" => "PROCEDURES",
                    "name" => "Процедуры",
                    "type" => "custom",
                    "value" => $proceduresSelectHtml,
                ],
                [
                    "id" => "TYPE",
                    "type" => "custom",
                    "value" => '<input type="hidden" name="TYPE" value="DOCTOR">'
                ],
                [
                    "id" => "ID",
                    "type" => "custom",
                    "value" => '<input type="hidden" name="ID" value="' . $doctorId . '">'
                ]
            ]
        ]
    ];

    $buttons = [
        "standard_buttons" => true,
        "custom_html" => '<input type="button" onclick="BX.SidePanel.Instance.close();" value="Закрыть">'
    ];

    $APPLICATION->IncludeComponent(
        'bitrix:ui.sidepanel.wrapper',
        '',
        [
            'POPUP_COMPONENT_NAME' => 'bitrix:main.interface.form',
            'POPUP_COMPONENT_TEMPLATE_NAME' => '',
            'POPUP_COMPONENT_PARAMS' => [
                "FORM_ID" => "doctor_edit_form",
                "TABS" => $tabs,
                "BUTTONS" => $buttons,
                "DATA" => []
            ]
        ]
    );

    die();
}

if ($request['IFRAME'] == 'Y' && $request['TYPE'] == 'PROCEDURES') {
    global $APPLICATION;
    $APPLICATION->RestartBuffer();
    $APPLICATION->SetTitle('Добавить процедуру');

    if ($procedureId) {
        $rsProcedure = ProceduresTable::query()
            ->setSelect([
                'NAME' => 'ELEMENT.NAME',
                'COLOR'
            ])
            ->setFilter([
                'ELEMENT.ID' => $procedureId
            ])
            ->fetch();

        $APPLICATION->SetTitle('Обновить процедуру');
    }

    $colorsHtml = [];
    if (!empty($rsProcedure['COLOR'])) {
        foreach ($rsProcedure['COLOR'] as $color) {
            $colorsHtml[] = "<div><input type='text' name='COLORS[]' value='{$color}'></div>";
        }
    }

    foreach (array_fill(0, 3, '') as $int) {
        $colorsHtml[] = "<div><input type='text' name='COLORS[]'></div>";
    }

    $tabs = [
        [
            "id" => "tab1",
            "name" => "Процедура",
            "title" => $rsProcedure['NAME'] ? "Обновить" : "Добавить",
            "fields" => [
                [
                    "id" => "NAME",
                    "name" => "Имя",
                    "type" => "text",
                    "value" => $rsProcedure['NAME'] ?? ""
                ],
                [
                    "id" => "COLOR",
                    "name" => "Цвета",
                    "type" => "custom",
                    "value" => implode(' ', $colorsHtml),
                ],
                [
                    "id" => "TYPE",
                    "type" => "custom",
                    "value" => '<input type="hidden" name="TYPE" value="PROCEDURES">'
                ],
                [
                    "id" => "ID",
                    "type" => "custom",
                    "value" => '<input type="hidden" name="ID" value="' . $procedureId . '">'
                ]
            ]
        ]
    ];

    $buttons = [
        "custom_html" => '<input type="button" onclick="BX.SidePanel.Instance.close();" value="Закрыть">'
    ];

    $APPLICATION->IncludeComponent(
        'bitrix:ui.sidepanel.wrapper',
        '',
        [
            'POPUP_COMPONENT_NAME' => 'bitrix:main.interface.form',
            'POPUP_COMPONENT_TEMPLATE_NAME' => '',
            'POPUP_COMPONENT_PARAMS' => [
                "FORM_ID" => "procedures_create_form",
                "TABS" => $tabs,
                "BUTTONS" => $buttons,
                "DATA" => []
            ],
            'RELOAD_GRID_AFTER_SAVE' => true
        ]
    );

    die();
}

$gridOptions = new Options($gridId);

$sort = $gridOptions->GetSorting([
    'sort' => ['ID' => 'ASC']
]);

$doctorsQuery = DoctorsTable::query()
    ->setSelect([
        'ID' => 'ELEMENT.ID',
        'NAME' => 'ELEMENT.NAME',
        'PROCEDURES',
        'PROCEDURES_ID'
    ])
    ->setOrder($sort['sort']);

$doctors = $doctorsQuery->fetchAll();

$columns = [
    ['id' => 'ID', 'name' => 'ID', 'sort' => 'ID', 'default' => true],
    ['id' => 'NAME', 'name' => 'Врач', 'sort' => 'NAME', 'default' => true],
    ['id' => 'PROCEDURES', 'name' => 'Процедуры', 'sort' => 'PROCEDURES', 'default' => true]
];

$list = [];

if (empty($procedures)) {
    $procedures = [];
}

$procedureColors = array_reduce($procedures, function ($acc, $procedure) {
    $acc[$procedure['ID']] = $procedure['COLOR'];
    return $acc;
}, []);

foreach ($doctors as $doctor) {

    $doctorProcedures = [];
    foreach ($doctor['PROCEDURES'] as $procedureId => $procedureName) {
        $colors = $procedureColors[$procedureId];

        if (empty($colors)) {
            $colors = [];
        }

        $colorsHtml = array_reduce($colors, function ($acc, $color) {
            $acc .= "<span style='background-color: {$color}; width: 20px; height: 20px; margin-right: 4px; position: relative; top: 5px; display: inline-block;'></span>";
            return $acc;
        }, '');

        $doctorProcedures[] = $procedureName . ' ' . $colorsHtml;
    }

    $list[] = [
        'id' => 'unique_row_id_' . $doctor['ID'],
        'data' => [
            'ID' => $doctor['ID'],
            'NAME' => $doctor['NAME'],
            'PROCEDURES' => implode(', ', $doctorProcedures)
        ],
        'actions' => [
            [
                'text' => 'Редактировать',
                'onclick' => "BX.SidePanel.Instance.open('?id={$doctor['ID']}&TYPE=DOCTOR')",
                'default' => true
            ],
            [
                'text' => 'Удалить',
                'onclick' => "deleteDoctor({$doctor['ID']})",
            ]
        ]
    ];
}

$addDoctorButton = new \Bitrix\UI\Buttons\AddButton(
    [
        "click" => new \Bitrix\UI\Buttons\JsCode(
            "BX.SidePanel.Instance.open('?TYPE=DOCTOR');"
        ),
        "text" => "Добавить врача"
    ]
);

$addProcedureButton = new \Bitrix\UI\Buttons\AddButton(
    [
        "click" => new \Bitrix\UI\Buttons\JsCode(
            "BX.SidePanel.Instance.open('?TYPE=PROCEDURES');"
        ),
        "text" => "Добавить процедуру"
    ]
);

\Bitrix\UI\Toolbar\Facade\Toolbar::addButton($addDoctorButton);
\Bitrix\UI\Toolbar\Facade\Toolbar::addButton($addProcedureButton);

$APPLICATION->IncludeComponent(
    'bitrix:main.ui.grid',
    '',
    [
        'GRID_ID' => $gridId,
        'COLUMNS' => $columns,
        'ROWS' => $list,

        'AJAX_MODE' => 'Y',
        'AJAX_ID' => \CAjax::getComponentID('bitrix:main.ui.grid', '.default', ''),
        'AJAX_OPTION_JUMP' => 'N',
        'AJAX_OPTION_HISTORY' => 'N',

        'SHOW_ROW_CHECKBOXES' => false,
        'SHOW_CHECK_ALL_CHECKBOXES' => false,
        'SHOW_ROW_ACTIONS_MENU' => true,
        'SHOW_GRID_SETTINGS_MENU' => true,
        'SHOW_NAVIGATION_PANEL' => true,
        'SHOW_PAGINATION' => false,

        'SHOW_SELECTED_COUNTER' => false,
        'SHOW_TOTAL_COUNTER' => false,
        'SHOW_PAGESIZE' => true,
        'ALLOW_COLUMNS_SORT' => true,
        'ALLOW_COLUMNS_RESIZE' => true,
        'ALLOW_HORIZONTAL_SCROLL' => true,
        'ALLOW_SORT' => true,
        'ALLOW_PIN_HEADER' => true,
    ]
);

?>
    <script>
        function deleteDoctor(id) {
            const messageBox = new BX.UI.Dialogs.MessageBox(
                {
                    message: "Вы действительно хотите удалить файл?",
                    title: "Подтверждение удаления",
                    buttons: BX.UI.Dialogs.MessageBoxButtons.OK_CANCEL,
                    okCaption: "Да",
                    onOk: function() {
                        BX.ajax({
                            url: window.location.href,
                            method: 'POST',
                            dataType: 'json',
                            data: {
                                ID: id,
                                TYPE: 'DELETE',
                                sessid: BX.bitrix_sessid()
                            },
                            onsuccess: function(response) {
                                var grid = BX.Main.gridManager.getById('<?=$gridId?>');
                                if (grid) {
                                    grid.instance.reloadTable();
                                }
                            },
                            onfailure: function(error) {
                                console.error('Error:', error);
                            }
                        });

                        messageBox.close();
                    },
                }
            );
            messageBox.show();
        }

        BX.ready(function () {
            BX.addCustomEvent("SidePanel.Slider:onCloseComplete", function () {
                var grid = BX.Main.gridManager.getById('<?=$gridId?>');
                if (grid) {
                    grid.instance.reloadTable();
                }
            });
        });
    </script>
<?php require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>
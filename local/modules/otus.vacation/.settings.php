<?php

use Bitrix\Main\DI\ServiceLocator;
use Otus\Vacation\UserRoles;
use Otus\Vacation\AccessManager;
use Otus\Vacation\Service;
use Otus\Vacation\Entity\VacationRequest;
use Otus\Vacation\Internal\VacationRequestTable;
use Otus\Vacation\Entity\VacationRequestApproval;
use Otus\Vacation\Internal\VacationRequestApprovalTable;
use Otus\Vacation\Entity\VacationItem;
use Otus\Vacation\Internal\VacationItemTable;
use Otus\Vacation\Entity\VacationItemApproval;
use Otus\Vacation\Internal\VacationItemApprovalTable;
use enoffspb\BitrixEntityManager\BitrixEntityManager;
use Bitrix\Main\Config\Option;

return [
    'services' => [
        'value' => [
            'otus.vacation.entityManager' => [
                'constructor' => static function () {
                    $entitiesConfig = [
                        VacationRequest::class => [
                            'tableClass' => VacationRequestTable::class,
                        ],
                        VacationRequestApproval::class => [
                            'tableClass' => VacationRequestApprovalTable::class,
                        ],
                        VacationItem::class => [
                            'tableClass' => VacationItemTable::class,
                        ],
                        VacationItemApproval::class => [
                            'tableClass' => VacationItemApprovalTable::class,
                        ],
                    ];

                    $config['entitiesConfig'] = $entitiesConfig;

                    return new BitrixEntityManager($config);
                }
            ],
            'otus.vacation.accessManager' => [
                'constructor' => static function () {
                    $module_id = 'otus.vacation';
                    // @TODO Получить из настроек модуля
                    $config = [
                        'roleGroupMap' => [
                            UserRoles::ROLE_MODERATOR => Option::get($module_id, "otus_vacation_moderator_id"),
                            UserRoles::ROLE_EMPLOYEE => Option::get($module_id, "otus_vacation_employee_id"),
                        ],
                        'roleUserMap' => [
                            UserRoles::ROLE_ACCOUNTANT => Option::get($module_id, "otus_vacation_account_id"),
                        ]
                    ];

                    return new AccessManager($config);
                }
            ],
            'otus.vacation.service' => [
                'constructor' => static function () {

                    $serviceLocator = ServiceLocator::getInstance();
                    $entityManager = $serviceLocator->get('otus.vacation.entityManager');

                    $connection = \Bitrix\Main\Application::getInstance()->getConnection();

                    return new Service($entityManager, $connection);
                }
            ],
        ]
    ]
];

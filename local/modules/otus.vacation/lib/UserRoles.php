<?php

namespace Otus\Vacation;

class UserRoles
{
    const ROLE_MODERATOR = 'moderator';
    const ROLE_EMPLOYEE = 'employee';
    const ROLE_ACCOUNTANT = 'accountant';

    private array $roles = [];

    public function __construct(int $userId, array $roleGroupMap, array $roleUserMap)
    {
        $userRoles = \Bitrix\Main\UserTable::getUserGroupIds($userId);

        foreach ($userRoles as $userRole) {

            $role = array_search($userRole, $roleGroupMap);

            if ($role) {
                $this->roles[] = $role;
            }
        }

        foreach ($roleUserMap as $rol => $roleUserId) {
            if($userId == $roleUserId) {
                $this->roles[] = $rol;
            }
        }

    }

    public function has(string $role)
    {
        return in_array($role, $this->roles);
    }
}

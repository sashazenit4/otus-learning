<?php

namespace Otus\Vacation;

abstract class AccessManagerBase
{
    protected $roleGroupMap = [];

    protected $roleUserMap = [];

    public function __construct(array $config)
    {
        if (array_key_exists('roleGroupMap', $config)) {
            $this->roleGroupMap = $config['roleGroupMap'];
        }

        if (array_key_exists('roleUserMap', $config)) {
            $this->roleUserMap = $config['roleUserMap'];
        }
    }

    public function getRoleGroupMap(): array
    {
        return $this->roleGroupMap;
    }

    public function can(int $userId, string $action, $context = null)
    {
        $actionMethod = "can" . $action;
        if (!method_exists($this, $actionMethod)) {
            throw new \Exception('Action ' . $action . ' is unknown.');
        }

        return $this->$actionMethod($userId, $context);
    }

    // @TODO Методы проверки прав ...
    // f.e.: protected function canCreateElement(int $userId): bool
    // f.e.: protected function canDeleteElement(int $userId, int $elementId): bool
}
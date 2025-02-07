<?php
namespace Otus\Events;

interface OnBeforeAddEventHandlerInterface
{
    /**
     * Метод вызывается перед добавлением элемента
     *
     * @param array $element Ссылка на добавляемый элемент
     * @return void
     */
    public function onBeforeAdd(array &$element);
}

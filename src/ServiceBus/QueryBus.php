<?php
declare(strict_types=1);

namespace App\ServiceBus;

interface QueryBus
{
    /**
     * @return object a promise object (not typehinted for now, as there's no standard class)
     */
    public function dispatch(object $query): object;
}

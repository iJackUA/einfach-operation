<?php

use einfach\operation\{Railway, Result};
use function einfach\operation\response\{ok, error};

class DeleteOperation extends ReadOperation implements \einfach\operation\IOperation
{
    public function railway() : Railway
    {
        return parent::railway()
            ->step([$this, 'delete']);
    }

    public function __invoke(array $params) : Result
    {
        return $this->railway()->runWithParams($params);
    }

    public function delete($params)
    {
        // pretend it is deleted from DB
        unset($params['model']);
        return ok($params);
    }
}

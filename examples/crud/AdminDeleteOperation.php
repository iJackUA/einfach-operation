<?php

use einfach\operation\{Railway, Result};
use function einfach\operation\response\{ok, error};

class AdminDeleteOperation extends DeleteOperation implements \einfach\operation\IOperation
{
    public function railway() : Railway
    {
        return parent::railway()
            ->failure([$this, 'trackAdminViolation']);
    }

    public function __invoke(array $params) : Result
    {
        return $this->railway()->runWithParams($params);
    }

    public function checkPermissions($params) 
    {
        return ( $params['user']->login == 'admin' ) 
                ? ok($params) 
                : error($params, 'Permission denied!');
    }

    public function trackAdminViolation($params) 
    {
        // write var_dump($params) to error log in case of failure
        return ok($params);
    }
}

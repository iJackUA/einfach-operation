<?php

use einfach\operation\{Railway, Result};
use function einfach\operation\response\{ok, error};

trait CRUDTraits 
{
    public function validate($params)
    {
        // pretend a lot of validations here
        if($params['price'] > 100) {
            return ok($params);
        } else {
            return error($params, 'Too cheap to be true!');
        }
    }

    public function checkPermissions($params)
    {
        return ( $params['user']->login == 'yevhen' ) 
                ? ok($params) 
                : error($params, 'Permission denied!');
    }
}

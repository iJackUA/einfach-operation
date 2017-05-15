<?php
require_once 'CRUDTraits.php';

use einfach\operation\{Railway, Result};
use function einfach\operation\response\{ok, error};

class UpdateOperation extends ReadOperation implements \einfach\operation\IOperation
{
    use CRUDTraits;
    
    public function railway() : Railway
    {
        return parent::railway()
            ->step([$this, 'update']);
    }

    public function __invoke(array $params) : Result
    {
        return $this->railway()->runWithParams($params);
    }

    public function update($params)
    {
        $params['model']->price = $params['price'];  
        $params['model']->name = $params['name'];
        //and pretend we save to DB
        return ok($params);
    }
}

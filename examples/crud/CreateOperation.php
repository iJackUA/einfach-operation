<?php
require_once 'CRUDTraits.php';

use einfach\operation\{Railway, Result};
use function einfach\operation\response\{ok, error};

class CreateOperation implements \einfach\operation\IOperation
{
    use CRUDTraits;

    public function railway() : Railway
    {
        return (new Railway)
            ->step([$this, 'validate'])
            ->step([$this, 'create']);
    }

    public function __invoke(array $params) : Result
    {
        return $this->railway()->runWithParams($params);
    }

    public function create($params)
    {
        // pretend it is saved to DB and ID assigned
        $model = [ 'id' => 10, 'price' => $params['price'], 'name' => $params['name']];
        return ok($params, [ 'model' => $model ]);
    }
}

<?php
require_once 'CRUDTraits.php';

use einfach\operation\{Railway, Result};
use function einfach\operation\response\{ok, error};

class ReadOperation implements \einfach\operation\IOperation
{
    use CRUDTraits;
    
    public function railway() : Railway
    {
        return (new Railway)
            ->step([$this, 'checkPermissions'])
            ->step([$this, 'getArticle'], ['name' => 'get']);
    }

    public function __invoke(array $params) : Result
    {
        return $this->railway()->runWithParams($params);
    }

    public function getArticle($params)
    {
        // pretend it is taken from DB
        $article = (object) [ 'id' => 10, 'price' => 149, 'name' => 'MacBook Pro 13' ];
        return ok($params, [ 'model' => $article ]);
    }
}

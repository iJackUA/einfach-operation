<?php

use einfach\operation\Railway;
use function einfach\operation\response\ok;
use function einfach\operation\response\error;

class UpdateOperation implements \einfach\operation\IOperation
{
    /**
     * @param $params
     * @return \einfach\operation\Result
     */
    public function __invoke($params)
    {
        $result = (new Railway)
            ->step(function ($params) {
                echo "Hey {$params['name']}. Say hello to anonymous function!";
                return ok(['newParam' => 'newValue']);
            })
//            ->step([$this, 'castRequest'])
//            ->step([$this, 'validateRequest'])
            ->step([$this, 'findUser'])
//            ->step([$this, 'updateDB'])
//            ->tryCatch([$this, 'sendNotification'])
//            ->always([$this, 'writeLog'])
//            ->fail([$this, 'notifyAdmin'])
            ->runWithParams($params);

        return $result;
    }

    public function castRequest($params)
    {
        return ok();
    }

    public function validateRequest($params)
    {
        return ok();
    }

    public function findUser($params)
    {
        // pretend I am doing a query
        // $user = DB::findById($params['id']);
        return ok(['model' => ['id' => 123, 'name' => 'Eugene', 'phone' => '111111']]);
    }

    public function updateDB($params)
    {
        return ok();
    }

    public function sendNotification($params)
    {
        return ok();
    }

    public function writeLog($params)
    {

    }

    public function notifyAdmin($params)
    {
        return ok();
    }
}


/*

$castRequest = castRequest($request);   // always success (one-way track) // Success
$validRequest = validateRequest($castRequest);  // true or false (two ways tracks)  // Step
$dbResult = updateDB($validRequest);  // does not return (dead-end track) // Step
sendNotification($dbResult, $validRequest);  // try catch // TryCatch
writeLog($dbResult, $validRequest); // supervisory (do smth for both tracks) // Proxy
render($dbResult, $validRequest);

*/


//->wrap(function ($params) use ($dbConn) {
//    /** @var $pipe Pipe */
//    $params['dbConn'] = $dbConn;
//
//    return Pipe::with($params)
//        ->tryCatch(function ($params) {
//            return $params['dbConn']->beginTransaction();
//        })
//        ->step(function ($params) {
//            return $params['dbConn']->createCommand('SQL #1')->execute();
//        })
//        ->step(function ($params) {
//            return $params['dbConn']->createCommand('SQL #2')->execute();
//        })
//        ->tryCatch(function ($params) {
//            return $params['transaction']->commit();
//        })
//        ->fail(function ($params) {
//            return $params['transaction']->rollBack();
//        })
//        ->run();
//})

<?php

use einfach\operation\Railway;
use einfach\operation\Result;
use function einfach\operation\response\ok;
use function einfach\operation\response\error;

class UpdateOperation implements \einfach\operation\IOperation
{
    public function railway() : Railway
    {
        return (new Railway)
            ->step(function ($params) {
                echo "Hey {$params['name']}. Say hello to anonymous function!";
                //return error($params, 'Early fail');
                return ok($params, ['newParam' => 'newValue']);
            }, ['name' => 'First'])
            ->step([$this, 'nestedRailway'])
            ->step([$this, 'castRequest'], ['name' => 'CastReq'])
            ->step([$this, 'validateRequest'])
            ->step(function ($params) {
                return error($params, 'AAA!!!');
            }, ['failFast' => true])
            ->step([$this, 'findUser'])
            ->step([$this, 'updateDB'])
            ->removeStep('CastReq')
            ->tryCatch([$this, 'sendNotification'])
            ->always([$this, 'writeLog'])
            ->failure([$this, 'notifyAdmin'], ['name' => 'Last'])
            ->step(function ($params) {
                return ok($params, ['a' => 'b']);
            }, ['after' => 'First', 'name' => 'FinalCheck']);
    }

    /**
     * @param array $params
     */
    public function __invoke(array $params) : Result
    {
        $result = $this->railway()->runWithParams($params);

        return $result;
    }

    public function nestedRailway($params)
    {
        return (new Railway)
            ->step(function ($params) {
                //return error($params, 'Nested Railway failed!');
                return ok($params, ['nestedRwParam' => 'nestedRwValue']);
            })
            ->runWithParams($params);
    }

    public function castRequest($params)
    {
        return ok($params);
    }

    public function validateRequest($params)
    {
        return ok($params);
    }

    public function findUser($params)
    {
        // pretend I am doing a query
        // $user = DB::findById($params['id']);
        $user = (object) ['id' => 123, 'name' => 'Eugene', 'phone' => '111111'];
        //return error($params, 'User not found!');    
        return ok($params, ['model' => $user]);
    }

    public function updateDB($params)
    {
        return ok($params);
    }

    public function sendNotification($params)
    {
        //throw new \Exception("Hey there, Exception!");
        return ok($params);
    }

    public function writeLog($params)
    {
        return ok($params);
    }

    public function notifyAdmin($params)
    {
        return ok($params);
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

// WRAPPER EXAMPLE WITH TRANSACTIONS
//->step(function ($params) use ($dbConn) {
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

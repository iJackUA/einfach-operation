<?php
use Railway\Pipe;

class UpdateOperation implements \Railway\IOperation
{
    public function __invoke($params)
    {
        $result = Pipe::with($params)
            ->step(function ($params) {
                echo "Say hello to anonymous function!";
                return \Railway\Response\Ok();
            })
            ->step([$this, 'castRequest'])
            ->step([$this, 'validateRequest'])
            ->tryCatch([$this, 'sendNotification'])
            ->proxy([$this, 'writeLog'])
            ->fail([$this, 'notifyBigBoss'])
            ->run();

        return $result;
    }

    protected function castRequest($params)
    {
        return \Railway\Response\Ok();
    }

    protected function validateRequest($params)
    {
        return \Railway\Response\Ok();
    }

    protected function updateDB($params)
    {
        return \Railway\Response\Ok();
    }

    protected function sendNotification($params)
    {
        return \Railway\Response\Ok();
    }

    protected function writeLog($params)
    {

    }

    protected function notifyBigBoss($params)
    {

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

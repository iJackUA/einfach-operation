<?php

use Railway\Pipe;

$request = [
    'id' => 123,
    'name' => 'Yevhen',
    'phone' => '123456789'
];

$castRequest = castRequest($request);   // always success (one-way track) // Success
$validRequest = validateRequest($castRequest);  // true or false (two ways tracks)  // Step
$dbResult = updateDB($validRequest);  // does not return (dead-end track) // Step
sendNotification($dbResult, $validRequest);  // try catch // TryCatch
writeLog($dbResult, $validRequest); // supervisory (do smth for both tracks) // Proxy
render($dbResult, $validRequest);


/** @var $result \Railway\Result */
$result = Pipe::with($request)
    ->step(function () {

    })
    ->step([])
    ->success(function () {

    })
    ->tryCatch(function () {

    })
    ->fail(function () {

    })
    ->run();


if ($result->isSuccess()) {
    render($result->value());
} else {
    render($result->error());
}


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


function castRequest($request)
{

}

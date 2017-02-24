<?php

use Railway\Pipe;
use Railway\Result;

$request = [
    'id' => 123,
    'name' => 'Yevhen',
    'phone' => '123456789'
];

$castRequest = castRequest($request);   // always success (one-way track)

$validRequest = validateRequest($castRequest);  // true or false (two ways tracks)

$dbResult = updateDB($validRequest);  // does not return (dead-end track)

sendInfoToExternalService();  // try catch

writeLog(); // supervisory (do smth for both tracks)

render();


$result = Pipe::with($request)
    ->step(function () {

    })
    ->success(function () {

    })
    ->tryCatch(function () {

    })
    ->wrap(function ($pipe) {
        /** @var $pipe Pipe */
        $pipe
            ->step(function () {

            })
            ->fail(function () {

            })
            ->run();
    })
    ->fail(function () {

    })
    ->run();


if ($result->isSuccess()) {
    render($result->value());
} else {
    render($result->error());
}






function castRequest($request)
{

}

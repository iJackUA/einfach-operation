<?php

$request = [
    'id' => 123,
    'name' => 'Yevhen',
    'phone' => '123456789'
];

$castRequest = castRequest($request);   // always success (one-way track)

$validRequest = validateRequest($castRequest);  // true or false (two ways tracks)

$result = updateDB($validRequest);  // does not return (dead-end track)

sendInfoToExternalService();  // try catch

writeLog(); // supervisory (do smth for both tracks)

printOutput();


Railway::pipe()
    ->wrap($request)
    ->step(function (){})
    ->success()
    ->run();


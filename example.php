<?php

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

printOutput();


$result = Railway::with($request)
    ->step(function (){})
    ->success()
    ->run();


if ($result->isSuccess()){
    echo $result->value();
} else {
    echo $result->value();
}








class Railway {
    function step($callable, $opt){

    }

    function success($callable, $opt){

    }

    function fail($callable, $opt){

    }
}



function castRequest($request){

}

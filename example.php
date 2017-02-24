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
    ->step(function () {

    })
    ->success()
    ->run();


if ($result->isSuccess()) {
    echo $result->value();
} else {
    echo $result->error();
}


class Railway
{

    protected $successTrack;
    protected $failureTrack;

    function __construct($params)
    {
        $this->successTrack = new SplQueue();
        $this->failureTrack = new SplQueue();
    }

    static function with($params)
    {
        return new static($params);
    }

    function step(callable $callable, $opt = [])
    {
        $this->successTrack->enqueue($callable);
    }

    function success(callable $callable, $opt = [])
    {
        $this->step($callable, array_merge($opt, ['alwaysSuccess' => true]));
    }

    function fail(callable $callable, $opt = [])
    {
        $this->failureTrack->enqueue($callable);
    }
}


function castRequest($request)
{

}

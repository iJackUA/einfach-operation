<?php
namespace Railway;

class Pipe
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

    /**
     *
     *
     * @param callable $callable Takes any notation from this list http://php.net/manual/en/language.types.callable.php
     * @param array $opt
     * @return $this
     */
    function step(callable $callable, $opt = [])
    {
        $this->successTrack->enqueue($callable);
        return $this;
    }

    function success(callable $callable, $opt = [])
    {
        $this->step($callable, array_merge($opt, ['Success' => true]));
        return $this;
    }

    function fail(callable $callable, $opt = [])
    {
        $this->failureTrack->enqueue($callable);
        return $this;
    }

    function tryCatch(callable $callable, $opt = []){
        return $this;
    }

    function wrap(callable $callable, $opt = []){
        // check if Result -> evaluate
        // if bool -> passthrough

        return $this;
    }

    /**
     * @return Result
     */
    function run()
    {

        // loop through success
        // on any success Step fails, stop success Track
        // start failure track and loop through it till the end

        foreach ($this->successTrack as $step) {

        }

        return new Result();
    }
}

<?php

namespace einfach\operation\step;

use einfach\operation\Railway;

class Failure extends AbstractStep
{
    function __invoke(&$params, string $track)
    {
        // only on Error track
        if ($track == Railway::TRACK_ERROR){
            return call_user_func($this->function, $params);
        }
    }
}

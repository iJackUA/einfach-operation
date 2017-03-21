<?php

namespace einfach\operation\step;

use einfach\operation\Railway;

class Failure extends AbstractStep
{
    function __invoke(&$params, string $track)
    {
        // works only on Error track
        if ($track == Railway::TRACK_FAILURE){
            call_user_func($this->function, $params);
            // does not respect function return and always back to error track
            return ['type' => Railway::TRACK_FAILURE];
        } else {
            $this->skipped = true;
        }
    }
}

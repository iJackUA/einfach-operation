<?php

namespace einfach\operation\step;

use einfach\operation\Railway;
use const einfach\operation\response\RESPONSE_TYPE_ERROR;

class Failure extends AbstractStep
{
    public function __invoke(&$params, string $track)
    {
        // works only on Error track
        if ($track == Railway::TRACK_FAILURE) {
            call_user_func($this->function, $params);
            // does not respect function return and always back to error track
            return ['type' => RESPONSE_TYPE_ERROR];
        } else {
            $this->skip();
        }
    }
}

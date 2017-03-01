<?php

namespace einfach\operation\step;

use einfach\operation\Railway;

class TryCatch extends AbstractStep
{
    function __invoke(&$params, string $track)
    {
        // only on OK track
        if ($track == Railway::TRACK_OK) {
            return call_user_func($this->function, $params);
        }
    }
}

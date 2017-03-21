<?php

namespace einfach\operation\step;

use einfach\operation\Railway;

class Wrap extends AbstractStep
{
    function __invoke(&$params, string $track)
    {
        // only on OK track
        if ($track == Railway::TRACK_SUCCESS) {
            return call_user_func($this->function, $params);
        } else {
            $this->skipped = true;
        }
    }
}

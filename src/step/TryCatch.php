<?php

namespace einfach\operation\step;

use einfach\operation\Railway;
use function einfach\operation\response\error;

class TryCatch extends AbstractStep
{
    function __invoke(&$params, string $track)
    {
        // only on OK track
        if ($track == Railway::TRACK_SUCCESS) {
            try {
                return call_user_func($this->function, $params);
            } catch (\Exception $e) {
                return error($e->getMessage());
            }
        } else {
            $this->skipped = true;
        }
    }
}

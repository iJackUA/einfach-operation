<?php

namespace einfach\operation\step;

use einfach\operation\Railway;

class Step extends AbstractStep
{
    public function __invoke(&$params, string $track)
    {
        // only on OK track
        if ($track == Railway::TRACK_SUCCESS) {
            $result = call_user_func($this->function, $params);
            return $this->normalizeStepResult($result);
        } else {
            $this->skip();
        }
    }
}

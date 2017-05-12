<?php

namespace einfach\operation\step;

use einfach\operation\Railway;

class Step extends AbstractStep
{
    public function __invoke(&$params, string $track)
    {
        // only on OK track
        if ($track == Railway::TRACK_SUCCESS) {
            $response = call_user_func($this->function, $params);
            return $this->normalizeStepResponse($response);
        } else {
            $this->skip();
        }
    }
}

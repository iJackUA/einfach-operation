<?php

namespace einfach\operation\step;

use einfach\operation\Railway;
use function einfach\operation\response\error;

class TryCatch extends AbstractStep
{
    public function __invoke(&$params, string $track)
    {
        // only on OK track
        if ($track == Railway::TRACK_SUCCESS) {
            try {
                $response = $this->normalizeStepResponse(call_user_func($this->function, $params));
                return $response;
            } catch (\Exception $e) {
                return $this->normalizeStepResponse(error($params, $e->getMessage()));
            }
        } else {
            $this->skip();
        }
    }
}

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
                $result = call_user_func($this->function, $params);
                return $this->normalizeStepResult($result);
            } catch (\Exception $e) {
                return error($e->getMessage());
            }
        } else {
            $this->skip();
        }
    }
}

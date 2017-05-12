<?php

namespace einfach\operation\step;

use einfach\operation\Railway;
use const einfach\operation\response\RESPONSE_TYPE_OK;
use const einfach\operation\response\RESPONSE_TYPE_ERROR;

class Always extends AbstractStep
{
    public function __invoke(&$params, string $track)
    {
        $response = $this->normalizeStepResponse(call_user_func($this->function, $params));
        // run on any track, but not change it to OK, if strated on Error
        $type = ($track == Railway::TRACK_FAILURE) ? RESPONSE_TYPE_ERROR : $response['type'];
        $response['type'] = $type;
        return $response;
    }
}

<?php

namespace einfach\operation\step;

use einfach\operation\Railway;
use const einfach\operation\response\RESPONSE_TYPE_OK;
use const einfach\operation\response\RESPONSE_TYPE_ERROR;

class Always extends AbstractStep
{
    public function __invoke(&$params, string $track)
    {
        // do not change track, not consider result
        call_user_func($this->function, $params);
        $type = ($track == Railway::TRACK_SUCCESS) ? RESPONSE_TYPE_OK : RESPONSE_TYPE_ERROR;
        return ['type' => $type];
    }
}

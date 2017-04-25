<?php

namespace einfach\operation\step;

class Always extends AbstractStep
{
    public function __invoke(&$params, string $track)
    {
        // do not change track, not consider result
        call_user_func($this->function, $params);
        return ['type' => $track];
    }
}

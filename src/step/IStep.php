<?php

namespace einfach\operation\step;

interface IStep
{
    public function __invoke(&$params);
}

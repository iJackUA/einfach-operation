<?php

namespace einfach\operation;

use einfach\operation\Railway;
use einfach\operation\Result;

interface IOperation
{
    public function __invoke(array $params) : Result;

    public function railway() : Railway;
}

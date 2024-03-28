<?php

namespace Teamone\DispatchClient\Builder;

use Teamone\DispatchClient\GuzzleClient;

interface GuzzleClientBuilder
{
    public function builder(): GuzzleClient;

    public function getConfig(): array;
}

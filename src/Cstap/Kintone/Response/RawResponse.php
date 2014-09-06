<?php

namespace Cstap\Kintone\Response;

use Guzzle\Service\Command\ResponseClassInterface;
use Guzzle\Service\Command\OperationCommand;

class RawResponse implements ResponseClassInterface
{
    public static function fromCommand(OperationCommand $command)
    {
        return $command->getResponse();
    }
}

<?php

declare(strict_types=1);

namespace Spiral\Shared\GRPC;

use Spiral\Core\CoreInterface;
use Spiral\RoadRunner\GRPC\Exception\GRPCException;
use Spiral\RoadRunner\GRPC\StatusCode;

class ServiceClientCore extends \Grpc\BaseStub implements CoreInterface
{
    public function callAction(string $controller, string $action, array $parameters = [])
    {
        /** @var RequestContext $ctx */
        $ctx = $parameters['ctx'];

        dump(
            (array) $ctx->getValue('metadata'),
            (array) $ctx->getValue('options'),
        );

        [$response, $status] = $this->_simpleRequest(
            $action,
            $parameters['in'],
            [$parameters['responseClass'], 'decode'],
            (array) $ctx->getValue('metadata'),
            (array) $ctx->getValue('options'),
        )->wait();

        $code = $status->code ?? StatusCode::UNKNOWN;

        if (!$status || $code !== StatusCode::OK) {
            throw new GRPCException(
                message: $status->details ?? '',
                code: $code
            );
        }

        return $response;
    }
}

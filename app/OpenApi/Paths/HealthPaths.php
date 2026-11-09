<?php

namespace App\OpenApi\Paths;

use OpenApi\Attributes as OA;

class HealthPaths
{
    #[OA\Get(
        path: '/api/health',
        operationId: 'healthCheck',
        summary: 'Health check',
        tags: ['Health'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Service is up',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'ok'),
                        new OA\Property(property: 'service', type: 'string', example: 'api-agenda'),
                    ]
                )
            ),
        ]
    )]
    public function health(): void
    {
    }
}

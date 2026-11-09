<?php

namespace App\OpenApi\Paths;

use OpenApi\Attributes as OA;

class ClientePaths
{
    #[OA\Get(
        path: '/api/clientes',
        operationId: 'clientesIndex',
        summary: 'List clients',
        security: [['sanctum' => []]],
        tags: ['Clientes'],
        parameters: [
            new OA\QueryParameter(name: 'q', description: 'Search by nome, email or cpf', required: false, schema: new OA\Schema(type: 'string')),
            new OA\QueryParameter(name: 'per_page', required: false, schema: new OA\Schema(type: 'integer', default: 15)),
            new OA\QueryParameter(name: 'page', required: false, schema: new OA\Schema(type: 'integer', default: 1)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Paginated clients',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/Cliente')),
                        new OA\Property(property: 'links', type: 'object'),
                        new OA\Property(property: 'meta', type: 'object'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function index(): void
    {
    }

    #[OA\Post(
        path: '/api/clientes',
        operationId: 'clientesStore',
        summary: 'Create a client',
        security: [['sanctum' => []]],
        tags: ['Clientes'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['nome', 'email', 'cpf'],
                properties: [
                    new OA\Property(property: 'nome', type: 'string', example: 'Ana Paula'),
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'ana@example.com'),
                    new OA\Property(property: 'cpf', type: 'string', example: '529.982.247-25'),
                    new OA\Property(property: 'telefone', type: 'string', nullable: true, example: '(11) 98888-7777'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Created',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', ref: '#/components/schemas/Cliente'),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function store(): void
    {
    }

    #[OA\Get(
        path: '/api/clientes/{cliente}',
        operationId: 'clientesShow',
        summary: 'Show a client',
        security: [['sanctum' => []]],
        tags: ['Clientes'],
        parameters: [
            new OA\PathParameter(name: 'cliente', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Client',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', ref: '#/components/schemas/Cliente'),
                    ]
                )
            ),
            new OA\Response(response: 404, description: 'Not found'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function show(): void
    {
    }

    #[OA\Put(
        path: '/api/clientes/{cliente}',
        operationId: 'clientesUpdate',
        summary: 'Update a client',
        security: [['sanctum' => []]],
        tags: ['Clientes'],
        parameters: [
            new OA\PathParameter(name: 'cliente', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'nome', type: 'string'),
                    new OA\Property(property: 'email', type: 'string', format: 'email'),
                    new OA\Property(property: 'cpf', type: 'string'),
                    new OA\Property(property: 'telefone', type: 'string', nullable: true),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Updated',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', ref: '#/components/schemas/Cliente'),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')
            ),
            new OA\Response(response: 404, description: 'Not found'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function update(): void
    {
    }

    #[OA\Delete(
        path: '/api/clientes/{cliente}',
        operationId: 'clientesDestroy',
        summary: 'Soft-delete a client',
        security: [['sanctum' => []]],
        tags: ['Clientes'],
        parameters: [
            new OA\PathParameter(name: 'cliente', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Deleted'),
            new OA\Response(response: 404, description: 'Not found'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function destroy(): void
    {
    }
}

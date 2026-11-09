<?php

namespace App\OpenApi\Paths;

use OpenApi\Attributes as OA;

class AtendimentoPaths
{
    #[OA\Get(
        path: '/api/atendimentos',
        operationId: 'atendimentosIndex',
        summary: 'List appointments',
        security: [['sanctum' => []]],
        tags: ['Atendimentos'],
        parameters: [
            new OA\QueryParameter(name: 'cliente_id', required: false, schema: new OA\Schema(type: 'integer')),
            new OA\QueryParameter(name: 'status', required: false, schema: new OA\Schema(type: 'string', enum: ['agendado', 'concluido', 'cancelado'])),
            new OA\QueryParameter(name: 'data_inicio', required: false, schema: new OA\Schema(type: 'string', format: 'date-time')),
            new OA\QueryParameter(name: 'data_fim', required: false, schema: new OA\Schema(type: 'string', format: 'date-time')),
            new OA\QueryParameter(name: 'per_page', required: false, schema: new OA\Schema(type: 'integer', default: 15)),
            new OA\QueryParameter(name: 'page', required: false, schema: new OA\Schema(type: 'integer', default: 1)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Paginated appointments',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/Atendimento')),
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

    #[OA\Get(
        path: '/api/clientes/{cliente}/atendimentos',
        operationId: 'clientesAtendimentosIndex',
        summary: 'List appointments for a client',
        security: [['sanctum' => []]],
        tags: ['Atendimentos'],
        parameters: [
            new OA\PathParameter(name: 'cliente', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\QueryParameter(name: 'status', required: false, schema: new OA\Schema(type: 'string', enum: ['agendado', 'concluido', 'cancelado'])),
            new OA\QueryParameter(name: 'data_inicio', required: false, schema: new OA\Schema(type: 'string', format: 'date-time')),
            new OA\QueryParameter(name: 'data_fim', required: false, schema: new OA\Schema(type: 'string', format: 'date-time')),
            new OA\QueryParameter(name: 'per_page', required: false, schema: new OA\Schema(type: 'integer', default: 15)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Paginated appointments for the client',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/Atendimento')),
                        new OA\Property(property: 'links', type: 'object'),
                        new OA\Property(property: 'meta', type: 'object'),
                    ]
                )
            ),
            new OA\Response(response: 404, description: 'Client not found'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function indexByCliente(): void
    {
    }

    #[OA\Post(
        path: '/api/atendimentos',
        operationId: 'atendimentosStore',
        summary: 'Create an appointment',
        security: [['sanctum' => []]],
        tags: ['Atendimentos'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['cliente_id', 'data_hora', 'status', 'assunto'],
                properties: [
                    new OA\Property(property: 'cliente_id', type: 'integer', example: 1),
                    new OA\Property(property: 'data_hora', type: 'string', format: 'date-time', example: '2026-11-20 14:30:00'),
                    new OA\Property(property: 'status', type: 'string', enum: ['agendado', 'concluido', 'cancelado'], example: 'agendado'),
                    new OA\Property(property: 'assunto', type: 'string', example: 'Primeira consulta'),
                    new OA\Property(property: 'observacoes', type: 'string', nullable: true, example: 'Trazer documentos'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Created',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', ref: '#/components/schemas/Atendimento'),
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
        path: '/api/atendimentos/{atendimento}',
        operationId: 'atendimentosShow',
        summary: 'Show an appointment',
        security: [['sanctum' => []]],
        tags: ['Atendimentos'],
        parameters: [
            new OA\PathParameter(name: 'atendimento', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Appointment',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', ref: '#/components/schemas/Atendimento'),
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
        path: '/api/atendimentos/{atendimento}',
        operationId: 'atendimentosUpdate',
        summary: 'Update an appointment',
        security: [['sanctum' => []]],
        tags: ['Atendimentos'],
        parameters: [
            new OA\PathParameter(name: 'atendimento', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'cliente_id', type: 'integer'),
                    new OA\Property(property: 'data_hora', type: 'string', format: 'date-time'),
                    new OA\Property(property: 'status', type: 'string', enum: ['agendado', 'concluido', 'cancelado']),
                    new OA\Property(property: 'assunto', type: 'string'),
                    new OA\Property(property: 'observacoes', type: 'string', nullable: true),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Updated',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', ref: '#/components/schemas/Atendimento'),
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
        path: '/api/atendimentos/{atendimento}',
        operationId: 'atendimentosDestroy',
        summary: 'Soft-delete an appointment',
        security: [['sanctum' => []]],
        tags: ['Atendimentos'],
        parameters: [
            new OA\PathParameter(name: 'atendimento', required: true, schema: new OA\Schema(type: 'integer')),
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

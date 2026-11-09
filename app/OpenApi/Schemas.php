<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'User',
    type: 'object',
    required: ['id', 'name', 'email'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Geovana Chaves'),
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'geovana@example.com'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ]
)]
#[OA\Schema(
    schema: 'AuthTokenResponse',
    type: 'object',
    required: ['user', 'token', 'token_type'],
    properties: [
        new OA\Property(property: 'user', ref: '#/components/schemas/User'),
        new OA\Property(property: 'token', type: 'string', example: '1|abcdef...'),
        new OA\Property(property: 'token_type', type: 'string', example: 'Bearer'),
    ]
)]
#[OA\Schema(
    schema: 'Cliente',
    type: 'object',
    required: ['id', 'nome', 'email', 'cpf'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'nome', type: 'string', example: 'Ana Paula'),
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'ana@example.com'),
        new OA\Property(property: 'cpf', type: 'string', example: '52998224725'),
        new OA\Property(property: 'telefone', type: 'string', nullable: true, example: '(11) 98888-7777'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ]
)]
#[OA\Schema(
    schema: 'Atendimento',
    type: 'object',
    required: ['id', 'cliente_id', 'data_hora', 'status', 'assunto'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'cliente_id', type: 'integer', example: 1),
        new OA\Property(property: 'data_hora', type: 'string', format: 'date-time', example: '2026-11-20T14:30:00.000000Z'),
        new OA\Property(property: 'status', type: 'string', enum: ['agendado', 'concluido', 'cancelado'], example: 'agendado'),
        new OA\Property(property: 'assunto', type: 'string', example: 'Primeira consulta'),
        new OA\Property(property: 'observacoes', type: 'string', nullable: true, example: 'Trazer documentos'),
        new OA\Property(property: 'cliente', ref: '#/components/schemas/Cliente', nullable: true),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ]
)]
#[OA\Schema(
    schema: 'ValidationError',
    type: 'object',
    properties: [
        new OA\Property(property: 'message', type: 'string', example: 'The given data was invalid.'),
        new OA\Property(
            property: 'errors',
            type: 'object',
            additionalProperties: new OA\AdditionalProperties(
                type: 'array',
                items: new OA\Items(type: 'string')
            ),
            example: ['email' => ['The email has already been taken.']]
        ),
    ]
)]
#[OA\Schema(
    schema: 'MessageResponse',
    type: 'object',
    properties: [
        new OA\Property(property: 'message', type: 'string', example: 'Logged out successfully.'),
    ]
)]
class Schemas
{
}

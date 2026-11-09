<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'API Agenda',
    description: 'REST API for clients and appointments (Laravel + Sanctum + SQL Server).'
)]
#[OA\Server(
    url: L5_SWAGGER_CONST_HOST,
    description: 'Application server'
)]
#[OA\SecurityScheme(
    securityScheme: 'sanctum',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'Token',
    description: 'Sanctum personal access token. Send as: Bearer {token}'
)]
#[OA\Tag(name: 'Health', description: 'Service health')]
#[OA\Tag(name: 'Auth', description: 'Registration and token authentication')]
#[OA\Tag(name: 'Clientes', description: 'Client CRUD')]
#[OA\Tag(name: 'Atendimentos', description: 'Appointment CRUD')]
class OpenApiSpec
{
}

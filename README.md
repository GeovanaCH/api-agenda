# API Agenda

![Testes](https://github.com/GeovanaCH/api-agenda/actions/workflows/tests.yml/badge.svg)
![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?logo=laravel&logoColor=white)
![SQL Server](https://img.shields.io/badge/SQL%20Server-2022-CC2927?logo=microsoftsqlserver&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-Compose-2496ED?logo=docker&logoColor=white)

API REST em **Laravel** para agenda de **clientes** e **atendimentos** — portfólio no mesmo espírito do [CRUD de clientes](https://github.com/GeovanaCH/crud-clientes) (SQL Server, Docker, testes), trocando PHP puro por Laravel.

> **Status:** marco 5 — CI (GitHub Actions) + OpenAPI (L5-Swagger). Próximo: deploy / seed demo.

## Stack

Laravel 13 (API) · Sanctum · PHP 8.3 · SQL Server 2022 · PDO `sqlsrv` · Apache · Docker Compose · PHPUnit · OpenAPI

## Como rodar

**Requisitos:** Docker com Docker Compose e cerca de **2 GB de RAM** livres para o SQL Server.

```bash
git clone https://github.com/GeovanaCH/api-agenda.git
cd api-agenda

cp .env.example .env
docker compose up -d --build

# Instala dependências, gera a APP_KEY e cria as tabelas
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate
```

Smoke test:

```bash
curl http://localhost:8080/api/health
# {"status":"ok","service":"api-agenda"}
```

Para abrir o banco em um cliente SQL: host `localhost`, porta `1433`, usuário `sa`, senha do `.env` e "Trust server certificate" marcado.

## Autenticação (Sanctum)

Tokens Bearer. Envie `Accept: application/json` e, nas rotas protegidas, `Authorization: Bearer <token>`.

### Registrar

```bash
curl -s -X POST http://localhost:8080/api/register \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Geovana Chaves",
    "email": "geovana@example.com",
    "password": "Password1!",
    "password_confirmation": "Password1!"
  }'
```

Resposta `201`: `user`, `token`, `token_type` (`Bearer`).

### Login

```bash
curl -s -X POST http://localhost:8080/api/login \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "geovana@example.com",
    "password": "Password1!"
  }'
```

### Usuário autenticado / logout

```bash
TOKEN="cole_o_token_aqui"

curl -s http://localhost:8080/api/me \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $TOKEN"

curl -s -X POST http://localhost:8080/api/logout \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $TOKEN"
```

## Clientes (auth)

Campos: `nome`, `email`, `cpf` (11 dígitos ou formatado), `telefone` (opcional). Soft delete em `excluido_em`. E-mail e CPF únicos entre clientes ativos. Busca: `?q=` (nome, e-mail ou CPF). Paginação: `?per_page=` (padrão 15).

### Listar / buscar

```bash
curl -s "http://localhost:8080/api/clientes?q=Ana&per_page=10" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $TOKEN"
```

### Criar

```bash
curl -s -X POST http://localhost:8080/api/clientes \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d '{
    "nome": "Ana Paula",
    "email": "ana@example.com",
    "cpf": "529.982.247-25",
    "telefone": "(11) 98888-7777"
  }'
```

### Ver / atualizar / excluir

```bash
curl -s http://localhost:8080/api/clientes/1 \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $TOKEN"

curl -s -X PUT http://localhost:8080/api/clientes/1 \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d '{
    "nome": "Ana Paula Silva",
    "email": "ana@example.com",
    "cpf": "529.982.247-25",
    "telefone": "(11) 97777-6666"
  }'

curl -s -X PATCH http://localhost:8080/api/clientes/1 \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d '{"telefone": "(11) 96666-5555"}'

curl -s -X DELETE http://localhost:8080/api/clientes/1 \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $TOKEN"
# 204 No Content
```

## Atendimentos (auth)

Campos: `cliente_id`, `data_hora`, `status` (`agendado` | `concluido` | `cancelado`), `assunto`, `observacoes` (opcional). Soft delete em `excluido_em`. Filtros: `cliente_id`, `status`, `data_inicio`, `data_fim`. Paginação: `?per_page=` (padrão 15).

### Listar / filtrar

```bash
curl -s "http://localhost:8080/api/atendimentos?cliente_id=1&status=agendado&data_inicio=2026-11-01&data_fim=2026-11-30" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $TOKEN"
```

### Criar

```bash
curl -s -X POST http://localhost:8080/api/atendimentos \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d '{
    "cliente_id": 1,
    "data_hora": "2026-11-20 14:30:00",
    "status": "agendado",
    "assunto": "Primeira consulta",
    "observacoes": "Trazer documentos"
  }'
```

### Ver / atualizar / excluir

```bash
curl -s http://localhost:8080/api/atendimentos/1 \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $TOKEN"

curl -s -X PUT http://localhost:8080/api/atendimentos/1 \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d '{
    "cliente_id": 1,
    "data_hora": "2026-11-20 15:00:00",
    "status": "concluido",
    "assunto": "Primeira consulta",
    "observacoes": "Finalizado"
  }'

curl -s -X PATCH http://localhost:8080/api/atendimentos/1 \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d '{"status": "cancelado"}'

curl -s -X DELETE http://localhost:8080/api/atendimentos/1 \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $TOKEN"
# 204 No Content
```

### Aninhado no cliente

```bash
curl -s "http://localhost:8080/api/clientes/1/atendimentos?status=agendado" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $TOKEN"
```

Erros de validação seguem o formato Laravel (`message` + `errors` por campo). Rotas protegidas sem token respondem `401`.

## Documentação OpenAPI

Swagger UI (L5-Swagger):

```text
http://localhost:8080/api/documentation
```

No Swagger, use **Authorize** com o token Sanctum (`Bearer <token>`). Para regenerar o JSON:

```bash
docker compose exec app php artisan l5-swagger:generate
# ou localmente:
php artisan l5-swagger:generate
```

Anotações em `app/OpenApi/` (auth, clientes, atendimentos). Com `L5_SWAGGER_GENERATE_ALWAYS=true` no `.env`, a UI regenera ao abrir.

## Testes e CI

PHPUnit roda em memória com SQLite (`phpunit.xml`) — sem SQL Server no CI.

```bash
docker compose exec app php artisan test
# ou, sem Docker:
php artisan test
```

GitHub Actions (`.github/workflows/tests.yml`) executa os testes e gera o OpenAPI a cada push/PR.

## Estrutura (resumo)

```
app/Enums              AtendimentoStatus
app/Http/Controllers   Auth, Cliente, Atendimento
app/Http/Requests      Auth/*, Cliente/*, Atendimento/*
app/Http/Resources     User, Cliente, Atendimento
app/Models             User, Cliente, Atendimento
app/OpenApi            Spec OpenAPI (L5-Swagger)
app/Support/Cpf.php    limpeza e dígitos verificadores
routes/api.php         health + auth + clientes + atendimentos
.github/workflows      CI PHPUnit
docker/                Apache/PHP + SQL Server
```

## Próximos marcos

1. ~~Scaffold — Laravel + Docker + `.env.example` + README~~
2. ~~Auth — Sanctum (register / login / logout / me) + testes~~
3. ~~Clientes — migration, model, Form Request, resource, CRUD + soft delete + busca~~
4. ~~Atendimentos — relacionamento, filtros por status/período, CRUD~~
5. ~~Qualidade — testes de feature, CI no Actions, OpenAPI~~
6. **Portfólio** — deploy, seed demo, README com curl e badge de testes

## Licença

Distribuído sob a licença MIT.

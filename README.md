# API Agenda

![Testes](https://github.com/GeovanaCH/api-agenda/actions/workflows/tests.yml/badge.svg)
![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?logo=laravel&logoColor=white)
![SQL Server](https://img.shields.io/badge/SQL%20Server-2022-CC2927?logo=microsoftsqlserver&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-Compose-2496ED?logo=docker&logoColor=white)
![OpenAPI](https://img.shields.io/badge/OpenAPI-3.0-85EA2D?logo=openapiinitiative&logoColor=white)

API REST em **Laravel** para agenda de **clientes** e **atendimentos** — portfólio no mesmo espírito do [CRUD de clientes](https://github.com/GeovanaCH/crud-clientes) (SQL Server, Docker, testes), trocando PHP puro por Laravel.

> **Demo principal:** Docker local (SQL Server 2022). Free tiers de PaaS quase nunca oferecem SQL Server — ver [docs/deploy.md](docs/deploy.md).

## O problema

Registrar clientes e seus atendimentos com autenticação por token, validação (incluindo CPF), exclusão lógica, busca/paginação e documentação OpenAPI — uma API pronta para integrar com qualquer front.

## Stack

Laravel 13 (API) · Sanctum · PHP 8.3 · SQL Server 2022 · PDO `sqlsrv` · Apache · Docker Compose · PHPUnit · L5-Swagger (OpenAPI) · GitHub Actions

## Como rodar (Docker)

**Requisitos:** Docker Compose e cerca de **2 GB de RAM** livres para o SQL Server.

```bash
git clone https://github.com/GeovanaCH/api-agenda.git
cd api-agenda

cp .env.example .env
docker compose up -d --build

docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
```

| Recurso | URL |
|---------|-----|
| Health | http://localhost:8080/api/health |
| OpenAPI (Swagger UI) | http://localhost:8080/api/documentation |
| SQL Server | `localhost:1433` · user `sa` · senha do `.env` · Trust server certificate |

### Usuário demo (seed)

| Campo | Valor |
|-------|-------|
| E-mail | `demo@api-agenda.test` |
| Senha | `Password1!` |

O seed também cria 2 clientes e 2 atendimentos de exemplo.

## Demo rápida (login → cliente → atendimento)

```bash
# 1) Login
TOKEN=$(curl -s -X POST http://localhost:8080/api/login \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{"email":"demo@api-agenda.test","password":"Password1!"}' \
  | php -r 'echo json_decode(stream_get_contents(STDIN))->token;')

echo "TOKEN=$TOKEN"

# 2) Criar cliente
curl -s -X POST http://localhost:8080/api/clientes \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d '{
    "nome": "Carla Mendes",
    "email": "carla@example.com",
    "cpf": "853.513.468-93",
    "telefone": "(11) 95555-4444"
  }'

# 3) Criar atendimento (ajuste cliente_id se necessário)
curl -s -X POST http://localhost:8080/api/atendimentos \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d '{
    "cliente_id": 1,
    "data_hora": "2026-12-01 09:00:00",
    "status": "agendado",
    "assunto": "Avaliação inicial",
    "observacoes": "Primeiro contato"
  }'

# 4) Listar
curl -s "http://localhost:8080/api/clientes?q=Carla" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $TOKEN"

curl -s "http://localhost:8080/api/atendimentos?status=agendado" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $TOKEN"
```

## Endpoints

### Auth
- `POST /api/register` · `POST /api/login` · `POST /api/logout` · `GET /api/me`

### Clientes (auth)
- `GET/POST /api/clientes` · `GET/PUT|PATCH/DELETE /api/clientes/{id}`
- Busca: `?q=` (nome, e-mail ou CPF) · paginação: `?per_page=`
- Soft delete (`excluido_em`); e-mail/CPF únicos entre ativos; CPF com dígitos verificadores

### Atendimentos (auth)
- `GET/POST /api/atendimentos` · `GET/PUT|PATCH/DELETE /api/atendimentos/{id}`
- Filtros: `cliente_id`, `status` (`agendado`\|`concluido`\|`cancelado`), `data_inicio`, `data_fim`
- Aninhado: `GET /api/clientes/{id}/atendimentos`

Erros de validação no formato Laravel (`message` + `errors`). Sem token → `401`.

## OpenAPI

Swagger UI: **http://localhost:8080/api/documentation**

Use **Authorize** com `Bearer <token>`. Regenerar:

```bash
docker compose exec app php artisan l5-swagger:generate
```

## Testes e CI

SQLite em memória no PHPUnit (sem SQL Server no CI). Badge acima aponta para o workflow.

```bash
docker compose exec app php artisan test
# ou localmente:
php artisan test
```

## Deploy / URL pública

Não há URL pública automática neste repositório: o banco de estudo é **SQL Server**, e planos gratuitos de Railway/Render/Fly tipicamente **não** incluem SQL Server.

- Demo suportada: **Docker local** (seção acima)
- Opções futuras (VPS, Azure SQL, etc.): [docs/deploy.md](docs/deploy.md)

## Estrutura

```
app/Http              Controllers, Form Requests, Resources
app/Models            User, Cliente, Atendimento
app/OpenApi           Spec L5-Swagger
app/Support           Cpf
database/seeders      DemoSeeder (usuário + dados de exemplo)
docker/               Apache/PHP + sqlsrv · init SQL Server
.github/workflows     CI PHPUnit + generate OpenAPI
docs/deploy.md        Notas de deploy
```

## Marcos

1. ~~Scaffold — Laravel + Docker + README~~
2. ~~Auth — Sanctum + testes~~
3. ~~Clientes — CRUD + soft delete + CPF~~
4. ~~Atendimentos — filtros + rota aninhada~~
5. ~~Qualidade — CI + OpenAPI~~
6. ~~Portfólio — seed demo + README + notas de deploy~~

## Licença

Distribuído sob a licença MIT.

# API Agenda

![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?logo=laravel&logoColor=white)
![SQL Server](https://img.shields.io/badge/SQL%20Server-2022-CC2927?logo=microsoftsqlserver&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-Compose-2496ED?logo=docker&logoColor=white)

API REST em **Laravel** para agenda de **clientes** e **atendimentos** — portfólio no mesmo espírito do [CRUD de clientes](https://github.com/GeovanaCH/crud-clientes) (SQL Server, Docker, testes), trocando PHP puro por Laravel.

> **Status:** marco 1 — scaffold (Laravel + Docker + SQL Server). Auth, CRUD e OpenAPI ainda não estão implementados.

## Stack

Laravel 13 (API) · PHP 8.3 · SQL Server 2022 · PDO `sqlsrv` · Apache · Docker Compose

## Como rodar

**Requisitos:** Docker com Docker Compose e cerca de **2 GB de RAM** livres para o SQL Server.

```bash
git clone https://github.com/GeovanaCH/api-agenda.git
cd api-agenda

cp .env.example .env
docker compose up -d --build

# Instala dependências e gera a APP_KEY (obrigatória no Laravel)
docker compose exec app composer install
docker compose exec app php artisan key:generate
```

Smoke test:

```bash
curl http://localhost:8080/api/health
# {"status":"ok","service":"api-agenda"}
```

Para abrir o banco em um cliente SQL: host `localhost`, porta `1433`, usuário `sa`, senha do `.env` e "Trust server certificate" marcado.

## Estrutura (resumo)

```
app/                 código da aplicação
routes/api.php       rotas da API (health por enquanto)
docker/php           imagem Apache + PHP + drivers sqlsrv
docker/sqlserver     script de criação do banco
docker-compose.yml   app + SQL Server 2022
```

## Próximos marcos

1. ~~Scaffold — Laravel + Docker + `.env.example` + README~~
2. **Auth** — Sanctum (register / login / logout / me) + testes
3. **Clientes** — migration, model, Form Request, resource, CRUD + soft delete + busca
4. **Atendimentos** — relacionamento, filtros por status/período, CRUD
5. **Qualidade** — testes de feature, CI no Actions, OpenAPI
6. **Portfólio** — deploy, seed demo, README com curl e badge de testes

## Licença

Distribuído sob a licença MIT.

-- Cria o banco da API (idempotente). Tabelas ficam a cargo das migrations do Laravel.
SET QUOTED_IDENTIFIER ON;
GO

IF DB_ID(N'api_agenda') IS NULL
BEGIN
    CREATE DATABASE api_agenda;
END
GO

# Adota Pet

Plataforma acadêmica para adoção responsável e proteção animal. O projeto reúne páginas de animais disponíveis, organizações de proteção, cadastro de doações, formulário de interesse em adoção, denúncias e autenticação de usuários.

## Tecnologias

- PHP 8.3 e Apache
- PDO com MySQL ou PostgreSQL
- HTML5, CSS3 e JavaScript
- Docker e Docker Compose
- Render Blueprint

## Início rápido com Docker

Pré-requisitos: Docker com o comando `docker compose` disponível.

```bash
docker compose up --build
```

A aplicação fica disponível em `http://localhost:8080`. O Compose inicia o MySQL, aguarda o banco ficar saudável, cria as tabelas automaticamente e então inicia o Apache.

Para encerrar:

```bash
docker compose down
```

Para também apagar os dados locais do banco:

```bash
docker compose down --volumes
```

## Configuração

Copie `.env.example` para `.env` se quiser substituir as credenciais locais usadas pelo Docker Compose. O arquivo `.env` é ignorado pelo Git.

Variáveis aceitas pela aplicação:

| Variável | Finalidade | Padrão local |
| --- | --- | --- |
| `DATABASE_URL` | URL completa do banco; tem prioridade sobre as demais variáveis | não definida |
| `DB_DRIVER` | `mysql` ou `pgsql` | `mysql` |
| `DB_HOST` | host do banco | `localhost` |
| `DB_PORT` | porta do banco | `3306` ou `5432` |
| `DB_NAME` | nome do banco | `adota_pet` |
| `DB_USER` | usuário do banco | `root` |
| `DB_PASS` | senha do banco | vazia |
| `RUN_MIGRATIONS` | executa os esquemas idempotentes ao iniciar o contêiner | `1` |
| `PORT` | porta em que o Apache escuta | `8080` |

Os esquemas versionados estão em `database/schema.mysql.sql` e `database/schema.pgsql.sql`.

## Execução sem Docker

Use PHP 8.3 com a extensão PDO correspondente ao seu banco e configure as variáveis de ambiente. Depois, crie as tabelas e inicie o servidor local:

```bash
php scripts/migrate.php
php -S 127.0.0.1:8080
```

## Implantação no Render

O arquivo `render.yaml` cria um serviço web Docker e um banco Render Postgres. A aplicação recebe a `DATABASE_URL`, executa o esquema PostgreSQL no início e expõe `/health.php` para verificação de saúde.

O Apache é configurado no início do contêiner para escutar a porta definida por `PORT`, como recomendado para serviços web do Render.

## Verificações

O smoke test valida os helpers de configuração, os formulários protegidos por CSRF, a ausência dos padrões de SQL vulneráveis corrigidos, os arquivos de infraestrutura e os links locais:

```bash
php tests/smoke.php
```

Para verificar a sintaxe de todos os arquivos PHP:

```bash
find . -name '*.php' -print0 | xargs -0 -n1 php -l
```

## Segurança implementada

- consultas preparadas em todos os cadastros e formulários;
- senhas armazenadas com `password_hash` e migração transparente de registros antigos em texto puro;
- validação de email, datas, limites de tamanho, CEP, telefone e UF no servidor;
- tokens CSRF nos formulários;
- cookies de sessão `HttpOnly`, `SameSite=Lax` e `Secure` em HTTPS;
- regeneração do identificador da sessão após autenticação;
- limitação temporária após tentativas repetidas de login;
- mensagens de erro públicas sem detalhes de conexão ou SQL;
- cabeçalhos HTTP de segurança no PHP e no Apache;
- credenciais fora do repositório.

## Estrutura principal

```text
components/       páginas, autenticação e rotinas PHP
css/              estilos da aplicação
database/         esquemas MySQL e PostgreSQL
docker/           configuração do Apache e inicialização
img/              imagens e recursos visuais
js/               interações da interface
scripts/          utilitários de manutenção do projeto
tests/            smoke test sem dependências externas
Dockerfile        imagem PHP 8.3 + Apache
docker-compose.yml ambiente local completo
render.yaml       infraestrutura no Render
```

## Contexto acadêmico

Este repositório registra um Projeto Integrador desenvolvido em grupo durante a faculdade. A interface, o conteúdo e o back-end resultam da colaboração entre integrantes da equipe.

# Adota Pet

O Adota Pet é uma plataforma acadêmica de adoção responsável e proteção animal. O projeto reúne animais disponíveis, organizações de proteção, cadastro de doações, formulários de interesse em adoção e denúncias, com área autenticada para usuários.

## Produção

- Aplicação: a URL pública será registrada aqui ao concluir o primeiro deploy.
- Hospedagem: Koyeb, instância web Free com deploy automático da branch `main`.
- Banco: Neon PostgreSQL, plano Free.
- Custo da infraestrutura configurada: R$ 0.

A instância gratuita pode entrar em suspensão quando fica sem tráfego. A primeira requisição após esse período pode levar alguns segundos.

## Tecnologias e arquitetura

- PHP 8.3 e Apache 2.4;
- PDO com PostgreSQL em produção e MySQL no ambiente local padrão;
- HTML5, CSS3 e JavaScript sem framework;
- Docker para build e execução reproduzíveis;
- Neon PostgreSQL com TLS obrigatório;
- Koyeb para build do `Dockerfile`, health check e entrega HTTPS.

Fluxo em produção:

```text
Navegador --HTTPS--> Koyeb/Apache/PHP --TLS--> Neon PostgreSQL
                         |
                         +--> migração idempotente na inicialização
```

As imagens, folhas de estilo e scripts são servidos pelo Apache. O PHP mantém a autenticação em sessão e acessa o banco exclusivamente por consultas preparadas.

## Execução local com Docker

Pré-requisito: Docker com o comando `docker compose`.

```bash
docker compose up --build
```

A aplicação fica disponível em `http://localhost:8080`. O Compose inicia o MySQL, espera o banco ficar saudável, cria as tabelas automaticamente e inicia o Apache.

Para encerrar:

```bash
docker compose down
```

Para apagar também os dados locais:

```bash
docker compose down --volumes
```

## Variáveis de ambiente

Copie `.env.example` para `.env` somente no desenvolvimento. `.env` é ignorado pelo Git e nunca deve ser versionado.

| Variável | Finalidade | Padrão local |
| --- | --- | --- |
| `DATABASE_URL` | URL completa e secreta do PostgreSQL; tem prioridade sobre as demais variáveis | não definida |
| `DB_DRIVER` | `mysql` ou `pgsql` | `mysql` |
| `DB_HOST` | host do banco | `localhost` |
| `DB_PORT` | porta do banco | `3306` ou `5432` |
| `DB_NAME` | nome do banco | `adota_pet` |
| `DB_USER` | usuário do banco | `root` |
| `DB_PASS` | senha do banco | vazia |
| `RUN_MIGRATIONS` | executa o esquema idempotente ao iniciar o contêiner | `1` |
| `PORT` | porta TCP em que o Apache escuta | `8080` |

Em produção, configure `DATABASE_URL` somente como secret no provedor. URLs do Neon devem usar `sslmode=require`; a aplicação também aplica TLS obrigatório automaticamente a hosts `*.neon.tech`.

## Execução sem Docker

Use PHP 8.3 com a extensão PDO correspondente ao banco, defina as variáveis de ambiente e execute:

```bash
php scripts/migrate.php
php -S 127.0.0.1:8080
```

## Banco e migrações

Os esquemas ficam em `database/schema.mysql.sql` e `database/schema.pgsql.sql`. Todos os `CREATE TABLE` usam `IF NOT EXISTS`, por isso a inicialização pode executar a migração mais de uma vez com segurança.

O entrypoint aguarda o banco ficar disponível antes de iniciar o Apache. Para executar a migração manualmente:

```bash
php scripts/migrate.php
```

## Deploy gratuito no Koyeb

1. Conecte o repositório GitHub `andertrunks/pii2-faculdade-pet-2`.
2. Selecione a branch `main` e o builder `Dockerfile`.
3. Escolha exclusivamente a instância `Free` em Frankfurt ou Washington, D.C.
4. Exponha a porta HTTP `8080` na rota `/`.
5. Configure o health check HTTP `GET /health.php` na porta `8080`.
6. Cadastre `DATABASE_URL` como secret e `RUN_MIGRATIONS=1` como variável comum.
7. Mantenha o autodeploy da branch `main` ativado.

Não selecione instâncias `nano`, `eco-*`, Standard, Pro ou trials. Elas não fazem parte desta configuração de custo zero.

## Verificações

Execute o smoke test:

```bash
php tests/smoke.php
```

Valide a sintaxe de todos os arquivos PHP no PowerShell:

```powershell
Get-ChildItem -Recurse -Filter *.php | ForEach-Object { php -l $_.FullName }
```

O endpoint `GET /health.php` testa a aplicação e a conexão com o banco. Ele retorna HTTP 200 com `{"status":"ok"}` quando saudável e HTTP 503 sem detalhes sensíveis quando o banco não está disponível.

## Segurança

- credenciais somente por variáveis de ambiente e secrets do provedor;
- PostgreSQL remoto protegido por TLS e channel binding quando fornecido;
- senhas de usuários armazenadas com `password_hash`;
- consultas preparadas em todos os fluxos de gravação e autenticação;
- validação de entrada e tokens CSRF nos formulários;
- cookies de sessão `HttpOnly`, `SameSite=Lax` e `Secure` em HTTPS;
- regeneração da sessão após login e limitação de tentativas;
- erros públicos sem detalhes de conexão ou SQL;
- PHP sem exposição de versão e com `display_errors=Off`;
- cabeçalhos HTTP de segurança e listagem de diretórios desativada.

## Estrutura

```text
components/       páginas, autenticação e rotinas PHP
css/              estilos da aplicação
database/         esquemas MySQL e PostgreSQL
docker/           configuração do Apache, PHP e entrypoint
img/              imagens e recursos visuais
js/               interações da interface
scripts/          migrações e utilitários
tests/            smoke tests sem dependências externas
Dockerfile        imagem PHP 8.3 + Apache
docker-compose.yml ambiente local com MySQL
```

## Contexto acadêmico

Este repositório registra um Projeto Integrador desenvolvido em grupo durante a faculdade. A interface, o conteúdo e o back-end resultam da colaboração entre integrantes da equipe.

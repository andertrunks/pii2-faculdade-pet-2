# Adota Pet

Plataforma acadêmica voltada à adoção responsável e à proteção animal. O projeto reúne informações sobre animais disponíveis para adoção, ONGs e canais de denúncia de maus-tratos.

> Projeto Integrador desenvolvido em grupo durante a faculdade. Anderson Costa atuou principalmente no back-end, trabalhando com PHP, conexão ao banco de dados e processamento de formulários, em um contexto de aprendizagem prática.

## Funcionalidades

- apresentação de animais disponíveis para adoção;
- formulário de interesse em adoção;
- cadastro de animais para doação;
- registro de denúncias de maus-tratos;
- área de login e cadastro;
- listagem de organizações de proteção animal;
- alternância entre tema claro e escuro.

## Tecnologias

- PHP 8.3
- MySQL e PDO
- HTML5, CSS3 e JavaScript
- Docker e Apache
- Render Blueprint

## Estrutura do projeto

```text
components/   páginas e rotinas PHP/HTML
css/          estilos da aplicação
img/          imagens e recursos visuais
js/           interações da interface
Dockerfile    ambiente PHP com Apache
render.yaml   configuração de implantação
```

## Execução local com Docker

É necessário ter Docker e um banco MySQL disponível.

```bash
docker build -t adota-pet .
docker run --rm -p 8080:80 \
  -e DB_HOST=host-do-banco \
  -e DB_USER=usuario \
  -e DB_PASS=senha \
  -e DB_NAME=adota-pet \
  adota-pet
```

Depois, acesse `http://localhost:8080`.

## Configuração do banco

A aplicação lê os dados de conexão pelas variáveis de ambiente abaixo:

| Variável | Finalidade |
| --- | --- |
| `DB_HOST` | endereço do servidor MySQL |
| `DB_USER` | usuário do banco |
| `DB_PASS` | senha do banco |
| `DB_NAME` | nome do banco |

## Contexto e aprendizados

Este repositório registra uma etapa da formação acadêmica da equipe. O desenvolvimento ajudou a praticar integração entre interface e servidor, formulários, sessões, persistência de dados, configuração por variáveis de ambiente e execução em contêiner.

## Aviso

O projeto tem finalidade educacional e representa o conhecimento da equipe no período em que foi produzido. Antes de qualquer uso em produção, ainda são necessárias revisões de segurança, validação de entradas, tratamento de erros, autenticação e proteção dos dados armazenados.

## Autoria

Desenvolvido em grupo como Projeto Integrador da faculdade. A interface, o conteúdo e o back-end resultam da colaboração entre integrantes da equipe. Este repositório não reivindica autoria individual sobre todo o projeto.

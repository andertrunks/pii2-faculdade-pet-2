CREATE TABLE IF NOT EXISTS cadastro (
    id_cadastro BIGSERIAL PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(254) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    cep VARCHAR(8) NOT NULL,
    rua VARCHAR(160) NOT NULL,
    cidade VARCHAR(120) NOT NULL,
    uf CHAR(2) NOT NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS animais (
    id_animal BIGSERIAL PRIMARY KEY,
    slug VARCHAR(80) NOT NULL UNIQUE,
    nome VARCHAR(120) NOT NULL,
    especie VARCHAR(40) NOT NULL,
    raca VARCHAR(120) NOT NULL,
    sexo VARCHAR(20) NOT NULL,
    idade_texto VARCHAR(40) NOT NULL,
    porte VARCHAR(30) NOT NULL,
    cidade VARCHAR(120) NOT NULL,
    uf CHAR(2) NOT NULL,
    descricao TEXT NOT NULL,
    imagem VARCHAR(255) NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'disponivel' CHECK (status IN ('disponivel', 'em_analise', 'adotado')),
    created_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS doar (
    id_doacao BIGSERIAL PRIMARY KEY,
    nome_pet VARCHAR(120) NOT NULL,
    idade_pet VARCHAR(40) NOT NULL,
    nome VARCHAR(120) NOT NULL,
    telefone VARCHAR(15) NOT NULL,
    email VARCHAR(254) NOT NULL,
    cep VARCHAR(8) NOT NULL,
    cidade VARCHAR(120) NOT NULL,
    uf CHAR(2) NOT NULL,
    sobre TEXT NOT NULL,
    user_id BIGINT NOT NULL CONSTRAINT doar_user_fk REFERENCES cadastro(id_cadastro) ON DELETE RESTRICT,
    created_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS denuncia (
    id_denuncia BIGSERIAL PRIMARY KEY,
    titulo VARCHAR(160) NOT NULL,
    data_denuncia DATE NOT NULL,
    descricao TEXT NOT NULL,
    user_id BIGINT NOT NULL CONSTRAINT denuncia_user_fk REFERENCES cadastro(id_cadastro) ON DELETE RESTRICT,
    created_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS adocao (
    id_adocao BIGSERIAL PRIMARY KEY,
    user_id BIGINT NOT NULL CONSTRAINT adocao_user_fk REFERENCES cadastro(id_cadastro) ON DELETE RESTRICT,
    animal_id BIGINT NOT NULL CONSTRAINT adocao_animal_fk REFERENCES animais(id_animal) ON DELETE RESTRICT,
    nome VARCHAR(120) NOT NULL,
    telefone VARCHAR(15) NOT NULL,
    idade SMALLINT NOT NULL CHECK (idade BETWEEN 18 AND 120),
    profissao VARCHAR(120) NOT NULL,
    residencia VARCHAR(120) NOT NULL,
    espaco VARCHAR(500) NOT NULL,
    acordo VARCHAR(500) NOT NULL,
    animais VARCHAR(500) NOT NULL,
    pq_animais TEXT NOT NULL,
    tempo VARCHAR(500) NOT NULL,
    deseja_adotar TEXT NOT NULL,
    ciente VARCHAR(500) NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'recebida' CHECK (status IN ('recebida', 'em_analise', 'aprovada', 'recusada')),
    created_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT adocao_user_animal_unique UNIQUE (user_id, animal_id)
);

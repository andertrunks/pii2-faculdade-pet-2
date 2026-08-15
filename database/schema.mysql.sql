CREATE TABLE IF NOT EXISTS cadastro (
    id_cadastro BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(254) NOT NULL,
    password VARCHAR(255) NOT NULL,
    cep VARCHAR(8) NOT NULL,
    rua VARCHAR(160) NOT NULL,
    cidade VARCHAR(120) NOT NULL,
    uf CHAR(2) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_cadastro),
    UNIQUE KEY cadastro_email_unique (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS animais (
    id_animal BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    slug VARCHAR(80) NOT NULL,
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
    status VARCHAR(20) NOT NULL DEFAULT 'disponivel',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_animal),
    UNIQUE KEY animais_slug_unique (slug),
    CONSTRAINT animais_status_check CHECK (status IN ('disponivel', 'em_analise', 'adotado'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS doar (
    id_doacao BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    nome_pet VARCHAR(120) NOT NULL,
    idade_pet VARCHAR(40) NOT NULL,
    nome VARCHAR(120) NOT NULL,
    telefone VARCHAR(15) NOT NULL,
    email VARCHAR(254) NOT NULL,
    cep VARCHAR(8) NOT NULL,
    cidade VARCHAR(120) NOT NULL,
    uf CHAR(2) NOT NULL,
    sobre TEXT NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_doacao),
    KEY doar_user_id_index (user_id),
    CONSTRAINT doar_user_fk FOREIGN KEY (user_id) REFERENCES cadastro (id_cadastro) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS denuncia (
    id_denuncia BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    titulo VARCHAR(160) NOT NULL,
    data_denuncia DATE NOT NULL,
    descricao TEXT NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_denuncia),
    KEY denuncia_user_id_index (user_id),
    CONSTRAINT denuncia_user_fk FOREIGN KEY (user_id) REFERENCES cadastro (id_cadastro) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS adocao (
    id_adocao BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    animal_id BIGINT UNSIGNED NOT NULL,
    nome VARCHAR(120) NOT NULL,
    telefone VARCHAR(15) NOT NULL,
    idade SMALLINT UNSIGNED NOT NULL,
    profissao VARCHAR(120) NOT NULL,
    residencia VARCHAR(120) NOT NULL,
    espaco VARCHAR(500) NOT NULL,
    acordo VARCHAR(500) NOT NULL,
    animais VARCHAR(500) NOT NULL,
    pq_animais TEXT NOT NULL,
    tempo VARCHAR(500) NOT NULL,
    deseja_adotar TEXT NOT NULL,
    ciente VARCHAR(500) NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'recebida',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_adocao),
    UNIQUE KEY adocao_user_animal_unique (user_id, animal_id),
    KEY adocao_animal_id_index (animal_id),
    CONSTRAINT adocao_user_fk FOREIGN KEY (user_id) REFERENCES cadastro (id_cadastro) ON DELETE RESTRICT,
    CONSTRAINT adocao_animal_fk FOREIGN KEY (animal_id) REFERENCES animais (id_animal) ON DELETE RESTRICT,
    CONSTRAINT adocao_status_check CHECK (status IN ('recebida', 'em_analise', 'aprovada', 'recusada'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

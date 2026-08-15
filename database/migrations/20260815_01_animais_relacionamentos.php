<?php

declare(strict_types=1);

return static function (PDO $pdo, string $driver): void {
    $columnExists = static function (string $table, string $column) use ($pdo, $driver): bool {
        $sql = $driver === 'pgsql'
            ? 'SELECT 1 FROM information_schema.columns WHERE table_schema = current_schema() AND table_name = :table AND column_name = :column'
            : 'SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = :table AND column_name = :column';
        $statement = $pdo->prepare($sql);
        $statement->execute(['table' => $table, 'column' => $column]);
        return $statement->fetchColumn() !== false;
    };

    $constraintExists = static function (string $table, string $constraint) use ($pdo, $driver): bool {
        $sql = $driver === 'pgsql'
            ? 'SELECT 1 FROM information_schema.table_constraints WHERE table_schema = current_schema() AND table_name = :table AND constraint_name = :constraint'
            : 'SELECT 1 FROM information_schema.table_constraints WHERE table_schema = DATABASE() AND table_name = :table AND constraint_name = :constraint';
        $statement = $pdo->prepare($sql);
        $statement->execute(['table' => $table, 'constraint' => $constraint]);
        return $statement->fetchColumn() !== false;
    };

    $indexExists = static function (string $table, string $index) use ($pdo, $driver): bool {
        $sql = $driver === 'pgsql'
            ? 'SELECT 1 FROM pg_indexes WHERE schemaname = current_schema() AND tablename = :table AND indexname = :index'
            : 'SELECT 1 FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = :table AND index_name = :index';
        $statement = $pdo->prepare($sql);
        $statement->execute(['table' => $table, 'index' => $index]);
        return $statement->fetchColumn() !== false;
    };

    $idType = $driver === 'pgsql' ? 'BIGINT' : 'BIGINT UNSIGNED';
    foreach (['doar', 'denuncia'] as $table) {
        if (!$columnExists($table, 'user_id')) {
            $pdo->exec("ALTER TABLE {$table} ADD COLUMN user_id {$idType} NULL");
        }
    }

    if (!$columnExists('adocao', 'user_id')) {
        $pdo->exec("ALTER TABLE adocao ADD COLUMN user_id {$idType} NULL");
    }
    if (!$columnExists('adocao', 'animal_id')) {
        $pdo->exec("ALTER TABLE adocao ADD COLUMN animal_id {$idType} NULL");
    }
    if (!$columnExists('adocao', 'status')) {
        $pdo->exec("ALTER TABLE adocao ADD COLUMN status VARCHAR(20) NOT NULL DEFAULT 'recebida'");
    }

    $foreignKeys = [
        ['doar', 'doar_user_fk', 'user_id', 'cadastro', 'id_cadastro'],
        ['denuncia', 'denuncia_user_fk', 'user_id', 'cadastro', 'id_cadastro'],
        ['adocao', 'adocao_user_fk', 'user_id', 'cadastro', 'id_cadastro'],
        ['adocao', 'adocao_animal_fk', 'animal_id', 'animais', 'id_animal'],
    ];
    foreach ($foreignKeys as [$table, $name, $column, $targetTable, $targetColumn]) {
        if (!$constraintExists($table, $name)) {
            $pdo->exec("ALTER TABLE {$table} ADD CONSTRAINT {$name} FOREIGN KEY ({$column}) REFERENCES {$targetTable} ({$targetColumn}) ON DELETE RESTRICT");
        }
    }

    $indexes = [
        ['doar', 'doar_user_id_index', 'user_id', false],
        ['denuncia', 'denuncia_user_id_index', 'user_id', false],
        ['adocao', 'adocao_animal_id_index', 'animal_id', false],
        ['adocao', 'adocao_user_animal_unique', 'user_id, animal_id', true],
    ];
    foreach ($indexes as [$table, $name, $columns, $unique]) {
        if (!$indexExists($table, $name)) {
            $pdo->exec('CREATE ' . ($unique ? 'UNIQUE ' : '') . "INDEX {$name} ON {$table} ({$columns})");
        }
    }

    $animals = [
        ['thomas', 'Thomas', 'Cão', 'Sem raça definida', 'Macho', '6 meses', 'Médio', 'São Paulo', 'SP', 'Thomas é brincalhão, sociável e está pronto para crescer ao lado de uma família responsável.', '../img/prod/a1.jpeg'],
        ['zoro', 'Zoro', 'Cão', 'Sem raça definida', 'Macho', '1 ano', 'Médio', 'Araraquara', 'SP', 'Zoro é curioso, carinhoso e gosta de passeios. Procura um lar com rotina ativa e segura.', '../img/prod/a2.jpg'],
        ['snow', 'Snow', 'Gato', 'Sem raça definida', 'Fêmea', '9 meses', 'Pequeno', 'Rio de Janeiro', 'RJ', 'Snow é tranquila e afetuosa. Adapta-se bem a ambientes internos protegidos e enriquecidos.', '../img/prod/a3.jpg'],
        ['pudim', 'Pudim', 'Cão', 'Sem raça definida', 'Macho', '4 meses', 'Pequeno', 'Rincão', 'SP', 'Pudim é um filhote alegre que precisa de acompanhamento veterinário, educação positiva e muito carinho.', '../img/prod/a4.avif'],
        ['lua', 'Lua', 'Gato', 'Sem raça definida', 'Fêmea', '4 meses', 'Pequeno', 'São Carlos', 'SP', 'Lua é uma filhote esperta e companheira, ideal para um lar telado e comprometido com sua segurança.', '../img/prod/a5.jpeg'],
        ['bolinha', 'Bolinha', 'Cão', 'Sem raça definida', 'Fêmea', '1 ano', 'Médio', 'Fortaleza', 'CE', 'Bolinha é dócil, atenta e adora companhia. Busca uma família que ofereça rotina e cuidados contínuos.', '../img/prod/a6.jpg'],
        ['bobby', 'Bobby', 'Cão', 'Sem raça definida', 'Macho', '1 ano', 'Grande', 'São Paulo', 'SP', 'Bobby é leal e cheio de energia. Precisa de espaço seguro, passeios e convivência com a família.', '../img/prod/a7.jpg'],
        ['chico', 'Chico', 'Gato', 'Sem raça definida', 'Macho', '1 ano e 3 meses', 'Pequeno', 'Rio de Janeiro', 'RJ', 'Chico é observador e carinhoso. Procura um lar tranquilo, com janelas teladas e adaptação paciente.', '../img/prod/a8.jpg'],
    ];

    $insertSql = $driver === 'pgsql'
        ? 'INSERT INTO animais (slug, nome, especie, raca, sexo, idade_texto, porte, cidade, uf, descricao, imagem) VALUES (:slug, :nome, :especie, :raca, :sexo, :idade_texto, :porte, :cidade, :uf, :descricao, :imagem) ON CONFLICT (slug) DO NOTHING'
        : 'INSERT IGNORE INTO animais (slug, nome, especie, raca, sexo, idade_texto, porte, cidade, uf, descricao, imagem) VALUES (:slug, :nome, :especie, :raca, :sexo, :idade_texto, :porte, :cidade, :uf, :descricao, :imagem)';
    $insert = $pdo->prepare($insertSql);
    foreach ($animals as [$slug, $nome, $especie, $raca, $sexo, $idade, $porte, $cidade, $uf, $descricao, $imagem]) {
        $insert->execute([
            'slug' => $slug,
            'nome' => $nome,
            'especie' => $especie,
            'raca' => $raca,
            'sexo' => $sexo,
            'idade_texto' => $idade,
            'porte' => $porte,
            'cidade' => $cidade,
            'uf' => $uf,
            'descricao' => $descricao,
            'imagem' => $imagem,
        ]);
    }
};

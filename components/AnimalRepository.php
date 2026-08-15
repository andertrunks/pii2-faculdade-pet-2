<?php

declare(strict_types=1);

final class AnimalRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function available(?string $species = null): array
    {
        $sql = "SELECT id_animal, slug, nome, especie, raca, sexo, idade_texto, porte, cidade, uf, descricao, imagem, status
                FROM animais WHERE status = 'disponivel'";
        $parameters = [];
        if ($species !== null) {
            $sql .= ' AND especie = :especie';
            $parameters['especie'] = $species;
        }
        $sql .= ' ORDER BY nome';

        $statement = $this->pdo->prepare($sql);
        $statement->execute($parameters);
        return $statement->fetchAll();
    }

    public function find(int $animalId): ?array
    {
        $statement = $this->pdo->prepare(
            'SELECT id_animal, slug, nome, especie, raca, sexo, idade_texto, porte, cidade, uf, descricao, imagem, status
             FROM animais WHERE id_animal = :animal_id LIMIT 1'
        );
        $statement->execute(['animal_id' => $animalId]);
        $animal = $statement->fetch();
        return $animal === false ? null : $animal;
    }

    public function related(string $species, int $excludedAnimalId, int $limit = 3): array
    {
        $statement = $this->pdo->prepare(
            "SELECT id_animal, nome, idade_texto, cidade, uf, imagem
             FROM animais
             WHERE especie = :especie AND id_animal <> :animal_id AND status = 'disponivel'
             ORDER BY nome LIMIT {$limit}"
        );
        $statement->execute(['especie' => $species, 'animal_id' => $excludedAnimalId]);
        return $statement->fetchAll();
    }
}

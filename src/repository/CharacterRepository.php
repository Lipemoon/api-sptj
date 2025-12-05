<?php

namespace Repository;

use Database\Database;
use Model\Character;
use PDO;

class CharacterRepository {
    private $connection;

    public function __construct() {
        $this->connection = Database::getConnection();
    }

    public function findAllCharacters(): array {
        $stmt = $this->connection->query("SELECT * FROM characters");
        //para cada linha de retorno, cria um objeto Character e aramazena em um array
        $characters = [];
        while ($row = $stmt->fetch()) {
            $character = new Character(
                id: $row["id"],
                name: $row["name"],
                gender: $row["gender"],
                game: $row["game"],
            );
            $characters[] = $character;
        }

        return $characters;
    }

     public function findByNameCharacter(string $name): array {
        $stmt = $this->connection->prepare("SELECT * FROM characters 
                                          WHERE name like :name");
        $stmt->bindValue(':name', '%' . $name . '%', PDO::PARAM_STR);
        $stmt->execute();

        $characters = [];
        while ($row = $stmt->fetch()) {
            $character = new Character(
                id: $row["id"],
                name: $row["name"],
                gender: $row["gender"],
                game: $row["game"],
            );
            $characters[] = $character;
        }
        return $characters;
    }


    public function findByIdCharacter(int $id): ?Character {
        $stmt = $this->connection->prepare("SELECT * FROM characters WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        //se não achou, retorna nulo
        $row = $stmt->fetch();
        if (!$row) {
            return null;
        }
        //se achou, cria um objeto Character
        $character = new Character(
            id: $row["id"],
            name: $row["name"],
            gender: $row["gender"],
            game: $row["game"],
        );
        //retorna o personagem encontrado
        return $character;
    }

    public function createCharacter(Character $character):Character {
        $stmt = $this->connection->prepare("INSERT INTO characters (name, gender, game) VALUES (:name, :gender, :game)");
        $stmt->bindValue(':name', $character->getName(), PDO::PARAM_STR);
        $stmt->bindValue(':gender', $character->getGender(), PDO::PARAM_STR);
        $stmt->bindValue(':game', $character->getGame(), PDO::PARAM_STR);
        $stmt->execute();
        return $character;
    }

    public function updateCharacter(Character $character):Character {
        $stmt = $this->connection->prepare("UPDATE characters SET name = :name, gender = :gender, game = :game WHERE id = :id");
        $stmt->bindValue(':id', $character->getId(), PDO::PARAM_INT);
        $stmt->bindValue(':name', $character->getName(), PDO::PARAM_STR);
        $stmt->bindValue(':gender', $character->getGender(), PDO::PARAM_STR);
        $stmt->bindValue(':game', $character->getGame(), PDO::PARAM_STR);
        $stmt->execute();
        return $character;
    }

    public function deleteCharacter(int $id): void {
        $stmt = $this->connection->prepare("DELETE FROM characters WHERE id = :id;");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function findAllCharactersByGameOriginAndGender(string $gameOrigin, string $gender): array {
        $stmt = $this->connection->prepare("SELECT * FROM characters WHERE game = :game AND gender = :gender");
        $stmt->bindValue(':game', $gameOrigin, PDO::PARAM_STR);
        $stmt->bindValue(':gender', $gender, PDO::PARAM_STR);
        $stmt->execute();

        $idsCharacters = [];
        while ($row = $stmt->fetch()) {
            $character = new Character(
                id: $row["id"],
                name: $row["name"],
                gender: $row["gender"],
                game: $row["game"],
            );
            $idsCharacters[] = $character->getId();
        }
        return $idsCharacters;
    }

    public function findAllCharactersByGameOrigin(string $gameOrigin): array {
        $stmt = $this->connection->prepare("SELECT * FROM characters WHERE game = :game");
        $stmt->bindValue(':game', $gameOrigin, PDO::PARAM_STR);
        $stmt->execute();

        $idsCharacters = [];
        while ($row = $stmt->fetch()) {
            $character = new Character(
                id: $row["id"],
                name: $row["name"],
                gender: $row["gender"],
                game: $row["game"],
            );
            $idsCharacters[] = $character->getId();
        }
        return $idsCharacters;
    }
}


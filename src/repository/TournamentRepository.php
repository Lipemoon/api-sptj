<?php

namespace Repository;

use Database\Database;
use Model\Tournament;
use PDO;

class TournamentRepository {
    private $connection;

    public function __construct() {
        $this->connection = Database::getConnection();
    }

    public function findAllTournaments(): array {
        $stmt = $this->connection->query("SELECT * FROM tournaments");
        $tournaments = [];
        while ($row = $stmt->fetch()) {
            $tournament = new Tournament(
                id: $row["id"],
                name: $row["name"],
                game: $row["game"],
                categoryByGenre: $row["categoryByGenre"],
                status: $row["status"],
                idsCharacters: json_decode($row["idsCharacters"] ?? '[]', true),
                idsCharactersWinnersOfRound: json_decode($row["idsCharactersWinnersOfRound"] ?? '[]', true),
                idsCharactersFighting: json_decode($row["idsCharactersFighting"] ?? '[]', true),
                winnerOfTournament: $row["winnerOfTournament"],
            );
            $tournaments[] = $tournament;
        }

        return $tournaments;
    }

    public function findByNameTournament(string $name): array {
        $stmt = $this->connection->prepare("SELECT * FROM tournaments 
                                          WHERE name like :name");
        $stmt->bindValue(':name', '%' . $name . '%', PDO::PARAM_STR);
        $stmt->execute();

        $tournaments = [];
        while ($row = $stmt->fetch()) {
            $tournament = new Tournament(
                id: $row["id"],
                name: $row["name"],
                game: $row["game"],
                categoryByGenre: $row["categoryByGenre"],
                status: $row["status"],
                idsCharacters: json_decode($row["idsCharacters"] ?? '[]', true),
                idsCharactersWinnersOfRound: json_decode($row["idsCharactersWinnersOfRound"] ?? '[]', true),
                idsCharactersFighting: json_decode($row["idsCharactersFighting"] ?? '[]', true),
                winnerOfTournament: $row["winnerOfTournament"],
            );
            $tournaments[] = $tournament;
        }

        return $tournaments;
    }


    public function findByIdTournament(int $id): ?Tournament {
        $stmt = $this->connection->prepare("SELECT * FROM tournaments WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        //se não achou, retorna nulo
        $row = $stmt->fetch();
        if (!$row) {
            return null;
        }
        //se achou, cria um objeto Tournament
        $tournament = new Tournament(
            id: $row["id"],
            name: $row["name"],
            game: $row["game"],
            categoryByGenre: $row["categoryByGenre"],
            status: $row["status"],
            idsCharacters: json_decode($row["idsCharacters"] ?? '[]', true),
            idsCharactersWinnersOfRound: json_decode($row["idsCharactersWinnersOfRound"] ?? '[]', true),
            idsCharactersFighting: json_decode($row["idsCharactersFighting"] ?? '[]', true),
            winnerOfTournament: $row["winnerOfTournament"],
            );
        //retorna o torneio encontrado
        return $tournament;
    }

    public function createTournament(Tournament $tournament):Tournament {
        $stmt = $this->connection->prepare("INSERT INTO tournaments (name, game, categoryByGenre) VALUES (:name, :game, :categoryByGenre)");
        $stmt->bindValue(':name', $tournament->getName(), PDO::PARAM_STR);
        $stmt->bindValue(':game', $tournament->getGame(), PDO::PARAM_STR);
        $stmt->bindValue(':categoryByGenre', $tournament->getCategoryByGenre(), PDO::PARAM_STR);
        $stmt->execute();
        return $tournament;
    }

    public function updateTournament(Tournament $tournament):Tournament {
        $stmt = $this->connection->prepare("UPDATE tournaments SET name = :name, game = :game, categoryByGenre = :categoryByGenre WHERE id = :id");
        $stmt->bindValue(':id', $tournament->getIdTournament(), PDO::PARAM_INT);
        $stmt->bindValue(':name', $tournament->getName(), PDO::PARAM_STR);
        $stmt->bindValue(':game', $tournament->getGame(), PDO::PARAM_STR);
        $stmt->bindValue(':categoryByGenre', $tournament->getCategoryByGenre(), PDO::PARAM_STR);
        $stmt->execute();
        return $tournament;
    }

    public function deleteTournament(int $id): void {
        $stmt = $this->connection->prepare("DELETE FROM tournaments WHERE id = :id;");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function save(Tournament $tournament) {
        $stmt = $this->connection->prepare("
            UPDATE tournaments SET 
                name = :name,
                game = :game,
                categoryByGenre = :categoryByGenre,
                status = :status,
                idsCharacters = :idsCharacters,
                idsCharactersWinnersOfRound = :idsCharactersWinnersOfRound,
                idsCharactersFighting = :idsCharactersFighting,
                winnerOfTournament = :winnerOfTournament
            WHERE id = :id
        ");

        $stmt->bindValue(':id', $tournament->getIdTournament(), PDO::PARAM_INT);
        $stmt->bindValue(':name', $tournament->getName());
        $stmt->bindValue(':game', $tournament->getGame());
        $stmt->bindValue(':categoryByGenre', $tournament->getCategoryByGenre());
        $stmt->bindValue(':status', $tournament->getStatus());

        $stmt->bindValue(':idsCharacters', json_encode($tournament->getIdsCharacters()));
        $stmt->bindValue(':idsCharactersWinnersOfRound', json_encode($tournament->getidsCharactersWinnersOfRound()));
        $stmt->bindValue(':idsCharactersFighting', json_encode($tournament->getIdsCharactersFighting()));
        $stmt->bindValue(':winnerOfTournament', $tournament->getWinnerOfTournament());

        $stmt->execute();

        return $tournament;
    }
    
}


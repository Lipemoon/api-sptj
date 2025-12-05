<?php

namespace Service;

use Error\APIException;
use Model\Tournament;
use Repository\TournamentRepository;
use Repository\CharacterRepository;

class TournamentService {
    private TournamentRepository $tournamentRepository;
    private CharacterRepository $characterRepository;

    public function __construct() {
        $this->tournamentRepository = new TournamentRepository();
        $this->characterRepository = new CharacterRepository();
    }

    public function getAllTournaments() {
        return $this->tournamentRepository->findAllTournaments();
    }

    public function getTournamentByName(?string $name): array {
        return $this->tournamentRepository->findByNameTournament($name);
    }

    public function getTournamentById(int $id) {
        $tournament = $this->tournamentRepository->findByIdTournament($id);
        if (!$tournament) {
            throw new APIException("Torneio não encontrado!", 404);
        } 
        return $tournament;
    }

    public function createTournament(Tournament $tournament) {
        if ($tournament->getCategoryByGenre() !== "Masculino" || 
            $tournament->getCategoryByGenre() !== "Feminino" || 
            $tournament->getCategoryByGenre() !== "Masculino e Feminino" ||
            $tournament->getCategoryByGenre() !== "Feminino e Masculino") {
            throw new APIException("Categoria de Gênero inválida!", 400);
        } 
        return $this->tournamentRepository->createTournament($tournament);
    }

     public function updateTournament(int $id, Tournament $tournament): Tournament {
        $existing = $this->tournamentRepository->findByIdTournament($id);

        if (!$existing) {
            throw new APIException("Torneio não encontrado!", 404);
        }

        if ($tournament->getCategoryByGenre() !== "Masculino" || 
            $tournament->getCategoryByGenre() !== "Feminino" || 
            $tournament->getCategoryByGenre() !== "Masculino e Feminino" ||
            $tournament->getCategoryByGenre() !== "Feminino e Masculino") {
            throw new APIException("Categoria de Gênero inválida!", 400);
        } 
        return $this->tournamentRepository->updateTournament($tournament);
    }

    public function deleteTournament(int $id) {
        $tournament = $this->tournamentRepository->findByIdTournament($id);

        if (!$tournament) {
            throw new APIException("Torneio não encontrado!", 404);
        } 
        $this->tournamentRepository->deleteTournament($id);
    }

    public function patch(int $id, array $updates) {
        $tournament = $this->tournamentRepository->findByIdTournament($id);

        if (!$tournament) {
            throw new APIException("Torneio não encontrado.", 404);
        }

        if (isset($updates["name"])) {
         $tournament->setName($updates["name"]);   
        }
        if (isset($updates["categoryByGenre"])) {
            $tournament->setCategoryByGenre($updates["categoryByGenre"]);
        } 
        if (isset($updates["game"])) {
            $tournament->setGame($updates["game"]);
        } 
        return $this->tournamentRepository->updateTournament($tournament);
    }

    public function startTournament(int $idTournament) {
        $tournament = $this->tournamentRepository->findByIdTournament($idTournament);
        if (!$tournament) {
            throw new APIException("Torneio não encontrado!", 404);
        }
        if ($tournament->getStatus() === "Torneio Iniciado") {
            throw new APIException("Torneio já está iniciado!", 400);
        }
        $idsCharacters = [];
        
        if ($tournament->getCategoryByGenre() === "Masculino e Feminino" ||
            $tournament->getCategoryByGenre() === "Feminino e Masculino") {
                $idsCharacters = $this->characterRepository->findAllCharactersByGameOrigin($tournament->getGame());
            } else {
                $idsCharacters = $this->characterRepository->findAllCharactersByGameOriginAndGender($tournament->getGame(), $tournament->getCategoryByGenre());
        }
        $tournament->setIdsCharacters($idsCharacters);
        $tournament->setStatus("Torneio Iniciado");
        $this->tournamentRepository->save($tournament);

        return [
            "message" => "Torneio iniciado com sucesso!",
            "id" => $tournament->getIdTournament(),
            "name" => $tournament->getName(),
            "game" => $tournament->getGame(),
            "status" => $tournament->getStatus(),
        ];
    }

    private function pegarPersonagensParaPartida(Tournament $tournament): array {
        $idsCharacters = $tournament->getIdsCharacters();
        $winnersOfRound = $tournament->getIdsCharactersWinnersOfRound() ?? [];

        // Se restou apenas 1 personagem na lista ele passa automaticamente para a proxima fase
        if (count($idsCharacters) === 1) {
            $winnersOfRound[] = $idsCharacters[0];
            $idsCharacters = [];
            $tournament->setIdsCharactersWinnersOfRound($winnersOfRound);
            $tournament->setIdsCharacters($idsCharacters);
        }

        // Se só tem 1 vencedor e ninguém mais na lista, quer dizer que ele venceu o torneio
        if (count($idsCharacters) === 0 && count($winnersOfRound) === 1) {
            $winnerId = $winnersOfRound[0];
            $tournament->setWinnerOfTournament($winnerId);
            $tournament->setIdsCharactersWinnersOfRound([]);
            $tournament->setStatus("Torneio Finalizado");
            $this->tournamentRepository->save($tournament);
            return [$winnerId];
        }       


        // Se acabou as rodadas, colocar todos que passaram no mesmo array novamente
        if (count($idsCharacters) === 0) {
            $idsCharacters = $winnersOfRound;
            $winnersOfRound = [];
            $tournament->setIdsCharacters($idsCharacters);
            $tournament->setIdsCharactersWinnersOfRound($winnersOfRound);
        }

        // Sorteia 2 Personagens para lutar
        // array_rand -> Escolhe um índice aleatório do array.
        $index1 = array_rand($idsCharacters);
        $idCharacter1 = $idsCharacters[$index1];
        // Remove do array o personagem sorteado, mas deixa buracos nos índices do array.
        unset($idsCharacters[$index1]);
        //array_values vai reorganizar o array e remover os buracos.
        $idsCharacters = array_values($idsCharacters);

        $index2 = array_rand($idsCharacters);
        $idCharacter2 = $idsCharacters[$index2];
        unset($idsCharacters[$index2]);
        $idsCharacters = array_values($idsCharacters);

        $charactersFighting = [$idCharacter1, $idCharacter2];

        $tournament->setIdsCharacters($idsCharacters);
        $tournament->setIdsCharactersFighting($charactersFighting);
        $tournament->setStatus("Torneio Iniciado");

        $this->tournamentRepository->save($tournament);

        return $tournament->getIdsCharactersFighting();
    }

    public function playMatch(int $idTournament) {
        $tournament = $this->tournamentRepository->findByIdTournament($idTournament);
        if (!$tournament) {
            throw new APIException("Torneio não encontrado!", 404);
        }
        if ($tournament->getStatus() === "Torneio Finalizado") {
            throw new APIException("Torneio já está finalizado!", 400);
        }

        if ($tournament->getStatus() === "Torneio não Iniciado") {
            throw new APIException("Torneio não está iniciado primeiro!", 400);
        }

        $charactersFighting = $tournament->getIdsCharactersFighting();

        if (count($charactersFighting) > 0) {
            throw new APIException("Há uma partida em andamento neste torneio!", 404);
        }

        $charactersFighting = $this->pegarPersonagensParaPartida($tournament);

        if (count($charactersFighting) === 1) {
            $characterWinner = $this->characterRepository->findByIdCharacter($charactersFighting[0]);
            return [
                "mensage" => "Torneio Finalizado, vencedor do Torneio " .  $characterWinner->getName(),
            ];  
        }

        $character1 = $this->characterRepository->findByIdCharacter($charactersFighting[0]);
        $character2 = $this->characterRepository->findByIdCharacter($charactersFighting[1]);

        return [
            "mensage" => "Confronto Iniciado!",
            "character1" => $character1,
            "character2" => $character2
        ];
    }
                
    public function chooseWinnerOfMatch(int $idTournament, int $idWinner) {
        $tournament = $this->tournamentRepository->findByIdTournament($idTournament);
        if (!$tournament) {
            throw new APIException("Torneio não encontrado!", 404);
        }

        if ($tournament->getStatus() === "Torneio Finalizado") {
            throw new APIException("Torneio já está finalizado!", 400);
        }

        if ($tournament->getStatus() === "Torneio não Iniciado") {
            throw new APIException("Torneio não está iniciado primeiro!", 400);
        }
        $charactersFighting = $tournament->getIdsCharactersFighting();

        if (count($charactersFighting) === 0) {
            throw new APIException("Não tem nenhuma partida em andamento neste torneio!", 404);
        }

        $idCharacter1 = $tournament->getIdsCharactersFighting()[0];
        $idCharacter2 = $tournament->getIdsCharactersFighting()[1];

        if ($idWinner !== $idCharacter1 && $idWinner !== $idCharacter2) {
            throw new APIException("O id do vencedor informado não existe nesta partida!", 400);
        }

        $character = $this->characterRepository->findByIdCharacter($idWinner);

        if (!$character) {
            throw new APIException("Personagem não encontrado!", 404);
        }
        $winners = $tournament->getIdsCharactersWinnersOfRound() ?? [];
        $winners[] = $character->getId();                    
        $tournament->setIdsCharactersWinnersOfRound($winners);  
        $tournament->setIdsCharactersFighting([]);
        $this->tournamentRepository->save($tournament);
        
        return [
            "message" => "Personagem {$character->getName()} ganhou a luta!"
        ];
    }

}
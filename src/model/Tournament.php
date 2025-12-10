<?php

namespace Model;

use JsonSerializable;

class Tournament implements JsonSerializable {
    private ?int $id;
    private string $name;
    private string $game;
    private string $categoryByGenre;
    private string $status;
    private array $idsCharacters;
    private array $idsCharactersWinnersOfRound;
    private array $idsCharactersFighting;
    private ?int $winnerOfTournament;
    
    public function __construct(
        string $name, string $game, string $categoryByGenre, string $status = "Torneio não Iniciado", array $idsCharacters = [], 
        array $idsCharactersWinnersOfRound = [], array $idsCharactersFighting = [], ?int $winnerOfTournament = null,
        ?int $id = null) {

        $this->id = $id;
        $this->name = $name;
        $this->game = $game;
        $this->categoryByGenre = $categoryByGenre;
        $this->status = $status;
        $this->idsCharacters = $idsCharacters;
        $this->idsCharactersWinnersOfRound = $idsCharactersWinnersOfRound;
        $this->idsCharactersFighting = $idsCharactersFighting;
        $this->winnerOfTournament = $winnerOfTournament;
    }

    //métodos GET
    public function getIdTournament(): int {
        return $this->id;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getGame(): string {
        return $this->game;
    }

    public function getCategoryByGenre(): string {
        return $this->categoryByGenre;
    }
    public function getStatus(): string {
        return $this->status;
    }

    public function getIdsCharacters(): array {
        return $this->idsCharacters;
    }
    public function getidsCharactersWinnersOfRound(): array {
        return $this->idsCharactersWinnersOfRound;
    }
    public function getWinnerOfTournament(): ?int {
        return $this->winnerOfTournament;
    }

    public function getIdsCharactersFighting(): array {
        return $this->idsCharactersFighting;
    }

    //métodos SET
    public function setIdTournament(int $id): void {
        $this->id = $id;
    }  
    public function setName(string $name): void {
        $this->name = $name;
    }      

    public function setIdsCharacters(array $idsCharacters) {
        $this->idsCharacters = $idsCharacters;
    }

    public function setGame(string $game): void {
        $this->game = $game;
    }

    public function setCategoryByGenre(string $categoryByGenre): void {
        $this->categoryByGenre = $categoryByGenre;
    }

    public function setStatus(string $status): void {
        $this->status = $status;
    }

    public function setIdsCharactersWinnersOfRound(array $charactersWinnersOfRound): void {
        $this->idsCharactersWinnersOfRound = $charactersWinnersOfRound;
    }

    public function setWinnerOfTournament(?int $winnerOfTournament): void {
        $this->winnerOfTournament = $winnerOfTournament;
    }
    
    public function setIdsCharactersFighting(array $idsCharactersFighting): void {
        $this->idsCharactersFighting = $idsCharactersFighting;
    }
    
    //a interface JsonSerializable exige a implementação desse método
    public function jsonSerialize(): array {
        $vars = get_object_vars($this);
        return $vars;
    }
}


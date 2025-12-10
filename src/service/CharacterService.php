<?php

namespace Service;

use Error\APIException;
use Model\Character;
use Repository\CharacterRepository;

class CharacterService {
    private CharacterRepository $characterRepository;

    public function __construct() {
        $this->characterRepository = new CharacterRepository();
    }

    public function getAllCharacters() {
        return $this->characterRepository->findAllCharacters();
    }

    public function getCharactersByName(?string $name): array {
        return $this->characterRepository->findByNameCharacter($name);
    }

    public function getCharacterById(int $id) {
        $character = $this->characterRepository->findByIdCharacter($id);
        if (!$character) {
            throw new APIException("Personagem não encontrado.", 404);
        } 
        return $character;
    }

    public function create(Character $character) {
        if ($character->getGender() === "Masculino" || $character->getGender() === "Feminino") {
            return $this->characterRepository->createCharacter($character);
        } 
        throw new APIException("Gênero inválido!", 400);
    }

     public function update(int $id, Character $character) {
        $existing = $this->characterRepository->findByIdCharacter($id);
        if (!$existing) {
            throw new APIException("Personagem não encontrado.", 404);
        }
        if ($character->getGender() === "Masculino" || $character->getGender() === "Feminino") {
            return $this->characterRepository->updateCharacter($character);
        } 
        throw new APIException("Gênero inválido!", 400);
    }

    public function delete(int $id) {
        $character = $this->characterRepository->findByIdCharacter($id);

        if (!$character) {
            throw new APIException("Personagem não encontrado.", 404);
        } 
        $this->characterRepository->deleteCharacter($id);
    }

    public function patch(int $id, array $updates) {
        $character = $this->characterRepository->findByIdCharacter($id);

        if (!$character) {
            throw new APIException("Personagem não encontrado.", 404);
        }
        if ($character->getGender() === "Masculino" || $character->getGender() === "Feminino") {
            if (isset($updates["name"])) {
                $character->setName($updates["name"]);   
            }
            if (isset($updates["gender"])) {
                $character->setGender($updates["gender"]);
            } 
            if (isset($updates["gameOrigin"])) {
                $character->setGame($updates["gameOrigin"]);
            } 
        } else {
            throw new APIException("Gênero inválido!", 400);
        }
        return $this->characterRepository->updateCharacter($character);
    }
}
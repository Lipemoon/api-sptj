<?php
namespace Controller;

use Error\APIException;
use Http\Request;
use Http\Response;
use Service\CharacterService;
use Model\Character;

class CharacterController {
    private CharacterService $characterService;

    public function __construct() {
        $this->characterService = new CharacterService();
    }

    public function processRequest(Request $request) {
        //recupera o método e o id da requisição
        $id = $request->getId();
        $method = $request->getMethod();

        //para as rotas que possuem um id (/characters/id):
        if ($id !== null) {
            switch ($method) { 
                case "GET":
                    //busca o personagem pelo seu id 
                    $response = $this->characterService->getCharacterById($id);
                    //retorna o personagem encontrado no formato JSON
                    Response::send($response);
                    break;
                case "PUT":
                    //verifica se o corpo da requisição está correto
                    $dados = $this->validateBody($request->getBody());
                    // cria objeto Character
                    $character = new Character(
                        name: $dados["name"],
                        gender: $dados["gender"],
                        game: $dados["game"]
                    );
                    $response = $this->characterService->update($id, $character);
                    Response::send($response);
                    break;
                case "PATCH":
                    $updates = $this->validatePatchBody($request->getBody());
                    $response = $this->characterService->patch($id, $updates);
                    Response::send($response);
                    break;
                case "DELETE":
                    //exclui o personagem especificado pelo id
                    $this->characterService->delete($id);
                    Response::send(null, 204);
                    break;
                default:
                    throw new APIException("Method not allowed!", 405);
            }
        } else {
            //para as rotas que não possuem um id (/characters)
            switch ($method) {
                case "GET":
                    //obtem o parâmetro de busca da querystring (se houver)
                    $name = $request->getQuery()["name"] ?? null;
                    if (!$name) {
                        $response = $this->characterService->getAllCharacters();
                    } else {
                        $response = $this->characterService->getCharactersByName($name);
                    }
                    Response::send($response);
                    break;
                case "POST":
                    //verifica se o corpo da requisição está correto
                    $dados = $this->validateBody($request->getBody());
                    $character = new Character(
                        name: $dados["name"],
                        gender: $dados["gender"],
                        game: $dados["game"]
                    );
                    $response = $this->characterService->create($character);
                    Response::send($response, 201);
                    break;
                default:
                    throw new APIException("Method not allowed!", 405);
            }
        }
    }

    
    private function validateBody(array $body): array {
        //cria um array para os dados do character que vierem no body
        $character = [];
            //verifica se o nome do personagem foi informado
            if (!isset($body["name"])) {
                throw new APIException("Name is required!", 400);
            }
            //verifica se o genero do personagem foi informado
            if (!isset($body["gender"])) {
                throw new APIException("Gender is required!", 400);
            }

            //verifica se o jogo do personagem foi informado
            if (!isset($body["game"])) {
                throw new APIException("Game is required!", 400);
            }

            //adiciona os dados já validados
            $character["name"] = $body["name"];
            $character["gender"] = $body["gender"];
            $character["game"] = $body["game"];
        
        return $character;
    }

    private function validatePatchBody(array $body): array {
        if (empty($body)) {
            throw new APIException("Nenhum campo fornecido para atualizar!", 400);
        }

        $campos = ["name", "gender", "game"];

        $updates = [];

        foreach ($body as $campo => $value) {
            if (!in_array($campo, $campos)) {
                throw new APIException("Campo '$campo' não pode ser atualizado!", 400);
            }

            $updates[$campo] = $value;
        }

        return $updates;
    }
}
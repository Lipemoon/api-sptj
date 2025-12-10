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

        if ($id !== null && !ctype_digit($id)) {
            throw new APIException("O ID deve ser um número inteiro válido!", 400);
        }

        $id = $id !== null ? (int)$id : null;
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
                    $character->setId($id);
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

    private function validarStringObrigatoria(string $valor, string $campo): string {
        $valor = trim($valor);

        if ($valor === '') {
            throw new APIException("O campo '$campo' não pode ser vazio!", 400);
        }

        return $valor;
    }
    
    private function validateBody(array $body): array {
       $required = ["name", "gender", "game"];

        foreach ($required as $campo) {
            if (!isset($body[$campo])) {
                throw new APIException("Campo '$campo' é obrigatório!", 400);
            }
            if (!is_string($body[$campo])) {
                throw new APIException("Campo '$campo' deve ser uma string!", 400);
            }

            // valida vazio ou só com espaços
            $body[$campo] = $this->validarStringObrigatoria($body[$campo], $campo);
        }

        return [
            "name" => $body["name"],
            "gender" => $body["gender"],
            "game" => $body["game"]
        ];
    }

    private function validatePatchBody(array $body): array {
        if (empty($body)) {
            throw new APIException("Nenhum campo fornecido para atualizar!", 400);
        }
        if (!is_string($body["name"])) {
            throw new APIException("Name tem que ser uma string!", 400);
        }
        if (!is_string($body["gender"])) {
            throw new APIException("Gender tem que ser uma string!", 400);
        }
        if (!is_string($body["game"])) {
            throw new APIException("Game tem que ser uma string!", 400);
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
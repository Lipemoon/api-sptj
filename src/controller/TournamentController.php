<?php
namespace Controller;

use Error\APIException;
use Http\Request;
use Http\Response;
use Model\Tournament;
use Service\TournamentService;

class TournamentController {
    private TournamentService $tournamentService;

    public function __construct() {
        $this->tournamentService = new TournamentService();
    }

    public function processRequest(Request $request) {
        $segments = $request->getSegments();
        $method = $request->getMethod();

        // Rotas customizadas
        if ($method === "POST" && ($request->getResource() === 'tournaments')) {
            // rota /api/tournaments/{id}/start
            if (isset($segments[1]) && is_numeric($segments[1]) && isset($segments[2]) && $segments[2] === 'start') {
                $idTournament = (int) $segments[1];
                $response = $this->tournamentService->startTournament($idTournament);
                Response::send($response);
                return;
            }
            // rota /api/tournaments/{id}/playMatch
            if (isset($segments[1]) && is_numeric($segments[1]) && isset($segments[2]) && $segments[2] === 'playMatch') {
                $idTournament = (int) $segments[1];
                $response = $this->tournamentService->playMatch($idTournament);
                Response::send($response);
                return;
            }
            // rota /api/tournaments/{id}/chooseWinner/{idWinner}
            if (isset($segments[1]) && is_numeric($segments[1]) && isset($segments[2]) && $segments[2] === 'chooseWinner' && isset($segments[3]) && is_numeric($segments[3])) {
                $idTournament = (int) $segments[1];
                $idWinner = (int) $segments[3];
                $response = $this->tournamentService->chooseWinnerOfMatch($idTournament, $idWinner);
                Response::send($response);
                return;
            }
        }

        $id = $request->getId();

        if ($id !== null && !ctype_digit($id)) {
            throw new APIException("O ID deve ser um número inteiro válido!", 400);
        }

        $id = $id !== null ? (int)$id : null;
    
        if ($id !== null) {
            switch ($method) {
                case "GET":
                    $response = $this->tournamentService->getTournamentById($id);
                    Response::send($response);
                    return;
                case "PUT":
                    $dados = $this->validateBody($request->getBody());
                    $tournament = new Tournament(
                        name: $dados["name"],
                        game: $dados["game"],
                        categoryByGenre: $dados["categoryByGenre"]
                    );
                    $tournament->setIdTournament($id);
                    $response = $this->tournamentService->updateTournament($id, $tournament);
                    Response::send($response);
                    return;
                case "PATCH":
                    $updates = $this->validatePatchBody($request->getBody());
                    $response = $this->tournamentService->patch($id, $updates);
                    Response::send($response);
                    return;
                case "DELETE":
                    $this->tournamentService->deleteTournament($id);
                    Response::send(null, 204);
                    return;
                default:
                    throw new APIException("Method not allowed!", 405);
            }
        } else {
            switch ($method) {
                case "GET":
                    $name = $request->getQuery()["name"] ?? null;
                    if (!$name) {
                        $response = $this->tournamentService->getAllTournaments();
                    } else {
                        $response = $this->tournamentService->getTournamentByName($name);
                    }
                    Response::send($response);
                    return;
                case "POST":
                    $dados = $this->validateBody($request->getBody());
                    $tournament = new Tournament(
                        name: $dados["name"],
                        game: $dados["game"],
                        categoryByGenre: $dados["categoryByGenre"]
                    );
                    $response = $this->tournamentService->createTournament($tournament);
                    Response::send($response, 201);
                    return;
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
       $required = ["name", "categoryByGenre", "game"];

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
            "gender" => $body["categoryByGenre"],
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

        if (!is_string($body["categoryByGenre"])) {
            throw new APIException("categoryByGenre tem que ser uma string!", 400);
        }

        if (!is_string($body["game"])) {
            throw new APIException("Game tem que ser uma string!", 400);
        }
        
        $campos = ["name", "categoryByGenre", "game"];

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


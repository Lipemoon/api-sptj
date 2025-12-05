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

        if (!is_numeric($id)) {
            $id = null;
        } else {
            $id = (int) $id;
        }
    
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

    private function validateBody(array $body): array {
        $tournament = [];

        if (!isset($body["name"])) {
            throw new APIException("Name is required!", 400);
        }

        if (!isset($body["game"])) {
            throw new APIException("game is required!", 400);
        }

        if (!isset($body["categoryByGenre"])) {
            throw new APIException("categoryByGenre is required!", 400);
        }

        $tournament["name"] = $body["name"];
        $tournament["game"] = $body["game"];
        $tournament["categoryByGenre"] = $body["categoryByGenre"];
        
        return $tournament;
    }

    private function validatePatchBody(array $body): array {
        if (empty($body)) {
            throw new APIException("Nenhum campo fornecido para atualizar!", 400);
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


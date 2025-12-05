<?php
// executa as configurações iniciais (autoload, tratamento de erros etc)
require_once 'src/config.php';

use Controller\CharacterController;
use Controller\TournamentController;
use Http\Request;
use Http\Response;

//cria um objeto para armazenar os principais dados da requisição
$uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER["REQUEST_METHOD"];
$body = file_get_contents("php://input");
$request = new Request($uri, $method, $body);

switch ($request->getResource()) { 
    //conforme o recurso solicitado
    case 'characters':
        //para todas as rotas iniciadas por /characters
        $characterController = new CharacterController();
        $characterController->processRequest($request);
        break;
    case 'tournaments':
        //para todas as rotas iniciadas por /tournaments
        $tournamentController = new TournamentController();
        $tournamentController->processRequest($request);
        break;
    default:
    //para a rota /
        $autor = ["Luis Felipe Nunes"];
        $endpoints = [
            "POST /api/characters",
            "POST /api/tournaments",
            "POST /api/tournaments/:idTournament/start",
            "POST /api/tournaments/:idTournament/playMatch",
            "POST /api/tournaments/:idTournament/chooseWinner/:idWinner",
            "GET /api/characters",
            "GET /api/characters?name=name",
            "GET /api/characters/:id",
            "GET /api/tournaments/:id",
            "GET /api/tournaments",
            "GET /api/tournaments?name=name",
            "PATCH /api/characters/:id",
            "PATCH /api/tournaments/:id",
            "PUT /api/characters/:id",
            "PUT /api/tournaments/:id",
            "DELETE /api/characters/:id",
            "DELETE /api/tournaments/:id"
        ];
        Response::send(["autor" => $autor, "rotas" => $endpoints]);
        break;
}
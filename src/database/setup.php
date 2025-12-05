<?php
// Este arquivo é responsável por configurar o banco de dados
// ele é executado por fora do fluxo normal da aplicação
// - a conexão com o banco de dados é feita diretamente aqui
// - não usa o tratamento de erros da API

// arquivo do banco de dados SQLite
$database = __DIR__ . '/database.sqlite';

try {
    // cria e configura a conexão com o banco de dados
    $conn = new PDO("sqlite:" . $database);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->exec("PRAGMA foreign_keys = ON;");
    echo "Conexão com o banco de dados estabelecida com sucesso!\n";
} catch (Exception $e) {
    echo "Erro ao conectar ao banco de dados: " . $e->getMessage() . PHP_EOL;
    exit;
}

try {
    // exclui as tabelas se já existirem
    $conn->exec("DROP TABLE IF EXISTS characters;");
    $conn->exec("DROP TABLE IF EXISTS tournaments;");

    // cria a tabela characters
    $sql = "CREATE TABLE characters (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        gender TEXT NOT NULL,
        game TEXT NOT NULL
    );";

    $conn->exec($sql);

    // cria a tabela tournaments
    $sql = "CREATE TABLE tournaments (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        game TEXT NOT NULL,
        categoryByGenre TEXT NOT NULL,
        status TEXT NOT NULL DEFAULT 'Torneio Não Iniciado',
        idsCharacters TEXT NULL,
        idsCharactersWinnersOfRound TEXT NULL,
        idsCharactersFighting TEXT NULL,
        winnerOfTournament INTEGER NULL,
        matchIsStarted BOOLEAN NOT NULL DEFAULT FALSE
    );";
    $conn->exec($sql);

    echo "Tabelas criadas com sucesso!" . PHP_EOL;
} catch (PDOException $e) {
    echo "Erro ao criar as tabelas: " . $e->getMessage() . PHP_EOL;
    exit;
}

//cria um conjunto de characters de exemplo
$characters = [
    ["name" => "Goku", "gender" => "Masculino", "game" => "Dragon Ball"],
    ["name" => "Supergirl", "gender" => "Feminino", "game" => "Injustice 2"],
    ["name" => "Mario", "gender" => "Masculino", "game" => "Super Mario"],
    ["name" => "Naruto", "gender" => "Masculino", "game" => "Naruto"],
];

//cria um cojunto de tournaments de exemplo
$tournaments = [
    ["name" => "Torneio Amigavel", "game" => "Naruto", "categoryByGenre" => "Masculino e Feminino"],
    ["name" => "Torneio Cooperativo", "game" => "Injustice 2", "categoryByGenre" => "Masculino e Feminino"],
    ["name" => "Torneio com Amigos", "game" => "Super Mario", "categoryByGenre" => "Masculino e Feminino"],
    ["name" => "Torneio De DBZ", "game" => "Dragon Ball", "categoryByGenre" => "Masculino e Feminino"],
];

try {
    // inicia uma transação
    $conn->beginTransaction();

    //salva o conjunto de characters no banco de dados
    $sql = "INSERT INTO characters (name, gender, game) VALUES 
    (:name, :gender, :game);";
    $stmt = $conn->prepare($sql);
    foreach ($characters as $character) {
        $stmt->execute($character); 
    }
    echo "Personagens inseridos com sucesso!" . PHP_EOL;

    //salva o conjunto de tournaments no banco de dados
    $sql = "INSERT INTO tournaments (id, name, game, categoryByGenre) 
            VALUES (:id, :name, :game, :categoryByGenre);";
    $stmt = $conn->prepare($sql);
    foreach ($tournaments as $tournament) {
        $stmt->execute($tournament); 
    }
    echo "Torneios inseridos com sucesso!" . PHP_EOL;

    // confirma a transação
    $conn->commit();
    echo "Banco de dados configurado com sucesso!" . PHP_EOL;
} catch (PDOException $e) {
    // reverte a transação em caso de erro
    if ($conn->inTransaction()) $conn->rollBack();
    echo "Erro ao inserir os dados: " . $e->getMessage() . PHP_EOL;
    exit;
}
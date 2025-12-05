# Sistema de Gerenciamento de Torneios de Jogos

# Feito por Luis Felipe Nunes

# Tema: 
<p> Desenvolver um Sistema de Gerenciamento de Torneios de
Jogos, onde os usuários podem cadastrar personagens de algum jogo
e criar um torneio daquele determinado jogo e iniciar um torneio onde
vai pegar todos os personagens cadastrados que tem relação com
aquele jogo para dentro do torneio e disputar uma batalha entre um e
outro, ou uma batalha entre equipes. </p>

# Regras de Negócio:
- O gênero do personagem tem que ser válido (Masculino ou
Feminino)
- O gênero do torneio tem que ser válido (Masculino, Feminino ou
ambos)
- Ao iniciar um torneio, todos os personagens que fazem parte do
jogo tem que entrar no torneio
- Ao iniciar uma batalha, tem que retornar 2 personagens para
batalhar
- Ao escolher o vencedor de uma batalha, o personagem avança e
espera os outros confrontos.

# Listagem das Rotas da API:
- POST api/characters
- POST api/tournaments
- GET api/characters
- GET /api/characters?name=name
- GET /api/characters/:id
- PATCH /api/characters/:id
- PATCH /api/tournaments/:id
- GET /api/tournaments/:id
- GET /api/tournaments
- GET /api/tournaments?name=name
- PUT /api/characters/:id
- PUT /api/tournaments/:id
- DELETE /api/characters/:id
- DELETE /api/tournaments/:id
- POST /api/tournaments/start/:id
- POST /api/tournaments/startMatch/:id
- POST /api/tournaments/:idTournament/chooseWinner/:idWinner
- POST /api/tournaments/:idTournament/teams/playMatch/:idMatch
- POST /api/tournaments/:idTournament/1v1/playMatch/:idMatch
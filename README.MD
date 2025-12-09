# Sistema de Gerenciamento de Torneios de Jogos

# Feito por Luis Felipe Nunes

# Tema: 
<p> Desenvolver um Sistema de Gerenciamento de Torneios de
Jogos, onde os usuários podem cadastrar personagens de algum jogo
e criar um torneio daquele determinado jogo e iniciar um torneio onde
vai pegar todos os personagens cadastrados que tem relação com
aquele jogo para dentro do torneio e disputar uma batalha entre um e
outro. </p>

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
- POST /api/characters,
- POST /api/tournaments,
- POST /api/tournaments/:idTournament/start,
- POST /api/tournaments/:idTournament/playMatch,
- POST /api/tournaments/:idTournament/chooseWinner/:idWinner,
- GET /api/characters,
- GET /api/characters?name=name,
- GET /api/characters/:id,
- GET /api/tournaments/:id,
- GET /api/tournaments,
- GET /api/tournaments?name=name,
- PATCH /api/characters/:id,
- PATCH /api/tournaments/:id,
- PUT /api/characters/:id,
- PUT /api/tournaments/:id,
- DELETE /api/characters/:id,
- DELETE /api/tournaments/:id

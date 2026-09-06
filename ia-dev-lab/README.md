# ia-dev-lab

Este é o laboratório da Atividade Prática Assíncrona da disciplina Desenvolvimento de Software com IA, no PPgTI do IMD-UFRN.

Eu montei uma lista de tarefas. A API fica em Laravel. A tela fica em React com TypeScript, Context API e Material UI. O ambiente sobe com Docker, para eu não instalar PHP, Node ou banco na minha máquina.

## Como está organizado

Eu separei o código por lado do sistema, não só por tipo de arquivo:

- backend: API e regra de negócio das tarefas
- frontend: tela das tarefas
- docs/adr: decisões de arquitetura
- AGENTS.md: contexto permanente da IA
- regras do Cursor: uma para o backend e outra para o frontend

## Como subir

1. Instale o Docker Desktop.
2. Abra o terminal na pasta ia-dev-lab.
3. Rode docker compose up --build -d
4. Abra a tela em http://localhost:5173
5. A API fica em http://localhost:8000/api/tasks

Não instale PHP, Node ou banco na máquina. Isso sobe nos containers.

## Comandos principais

Rode na pasta ia-dev-lab.

- docker compose up --build -d sobe o ambiente inteiro
- docker compose down para o ambiente inteiro
- docker compose exec backend php artisan test testa a API
- docker compose exec backend php artisan test --testsuite=Unit testa a regra de negócio da API
- docker compose exec backend php artisan test --testsuite=Feature testa as rotas da API
- docker compose exec frontend npm test testa a tela
- docker compose exec backend php artisan migrate roda as migrations

## Decisões

Eu registrei as escolhas deste laboratório nas ADRs, na pasta docs/adr.

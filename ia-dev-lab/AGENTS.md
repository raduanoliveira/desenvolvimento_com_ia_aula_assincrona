# AGENTS.md

## Sobre o projeto

ToDo list em monorepo: API REST em Laravel e interface em React com TypeScript, Context API e Material UI. O layout deve ser moderno. O ambiente sobe com Docker. O código segue TDD, SOLID, camadas do POSA (Layers) e padrões GoF quando foi necessário.

## Comandos

Rode na pasta do laboratório.

- `docker compose up --build -d` -> sobe o ambiente inteiro em segundo plano
- `docker compose down` -> para o ambiente inteiro
- `docker compose exec backend php artisan test` -> todos os testes do backend
- `docker compose exec backend php artisan test --testsuite=Unit` -> testes de unidade do backend
- `docker compose exec backend php artisan test --testsuite=Feature` -> testes de feature/API do backend
- `docker compose exec frontend npm test` -> todos os testes do frontend
- `docker compose exec backend php artisan migrate` -> roda migrations

## Convenções de código

- TDD: teste primeiro, depois o código
- Regra de negócio na camada de aplicação (Service), não no controller e não na tela
- Validação na entrada da API, fora do controller
- Persistência atrás de abstração (Repository), não acoplada à regra de negócio
- Frontend com TypeScript, estado compartilhado no Context e UI em MUI
- Layout moderno: hierarquia clara, espaçamento generoso, cards e status visível. Não entregue tela crua
- Organize por domínio (API de um lado, interface do outro)
- SOLID:
  - S: cada peça tem um motivo para mudar (HTTP, caso de uso, persistência, tela, estado, acesso à API)
  - O: caso de uso novo vira peça nova, sem inchar o que já existe
  - L: quem depende de uma abstração deve funcionar com qualquer implementação dela
  - I: interfaces pequenas, só com o que o recurso precisa
  - D: depender de abstração; injetar colaboração, não instanciar na mão
- POSA (Layers): cada camada só fala com a de baixo. Não pule camada.
  - Backend: apresentação -> aplicação -> persistência
  - Frontend: apresentação -> estado -> acesso à API
- GoF, quando foi necessário:
  - Command: um caso de uso por serviço
  - Adapter: a persistência concreta se adapta à abstração
  - Factory: o container monta as dependências
  - Template Method: o fluxo comum fica no tipo base e os passos que variam ficam nas especializações
  - Observer ou Strategy só se houver efeito colateral ou mais de um algoritmo
- Tipos de teste:
  - Backend, unidade: regra de negócio, sem HTTP e sem banco
  - Backend, feature/API: rotas, status HTTP, JSON e persistência
  - Frontend, unidade: o acesso à API (método e payload)
  - Frontend, integração: o estado compartilhado, com a API mockada
  - Frontend, componente: formulário, lista e casca da tela. A tela não chama a API direto
  - Sem teste E2E neste projeto

## Não fazer

- Não colocar regra de negócio no controller ou na tela
- Não pular camada
- Não aplicar padrão GoF por aplicar; use quando foi necessário
- Não pular testes nem misturar os tipos
- Não entregar layout datado ou tela só com campo e lista
- Não instalar PHP, Node ou banco na máquina host
- Não misturar backend e frontend na mesma pasta
- Não versionar segredos nem pastas de dependência

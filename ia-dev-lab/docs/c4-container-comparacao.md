# Comparação das duas versões do diagrama C4

Os diagramas também estão em PNG (`c4-container-v1.png` e `c4-container-v2.png`) e em Mermaid `flowchart`, porque o bloco `C4Container` não renderiza neste editor.

## O que cada uma mostra

A versão A desenha cinco caixas *dentro* do ToDo: frontend, API, Services, Repository e MySQL, mais Google. A versão B desenha dois processos (React :5173 e Laravel :8000), o SQLite e os dois provedores de login.

## Qual comunica melhor

A **versão B**.

No C4 de contêiner, a unidade é processo ou armazenamento que se implanta. Service e repositório não se implantam sozinhos neste laboratório: são camadas *dentro* do backend. A versão A trata POSA como topologia de rede e ainda troca o SQLite por MySQL, que o `docker-compose.yml` não sobe. Quem lê a A pode achar que existe um microserviço de regra de negócio e outro de dados.

A versão B deixa o contrato visível: a tela fala com a API por REST e cookie; a API fala com o SQLite e com Google/GitHub. `Domain\Task` e Eloquent cabem na descrição do backend, não em caixas novas — alinhado à decisão do ADR 0005 (tamanho certo, contrato fechado no processo da API).

A versão A ainda omite o GitHub, que é o segundo provedor já em produção no laboratório.

## O que a investigação mostrou

O prompt curto não basta para C4. Sem dizer o que *não* é contêiner, o agente promove camada a processo. O segundo prompt, com portas, banco real e a regra “Eloquent não é contêiner”, mais o corte manual das caixas extras, é o que comunica a arquitetura deste ToDo.

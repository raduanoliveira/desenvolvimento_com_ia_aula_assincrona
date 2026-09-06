# Constituição do ia-dev-lab

Princípios não negociáveis. Toda spec, plano e código deste laboratório deve respeitá-los. Fonte irmã: `AGENTS.md`.

## Core Principles

### I. Spec antes do código
A especificação é a fonte da verdade do que o sistema deve fazer. Não se começa pela implementação. Se o código e a spec divergirem, corrige-se o código ou volta-se a especificar — não se “aceita o que a IA achou melhor”.

### II. Teste primeiro (não negociável)
TDD: o teste do comportamento nasce da spec (critérios Given/When/Then) antes da implementação. Tipos não se misturam: unidade (regra, sem HTTP e sem banco), feature/API (rota e persistência), frontend de gateway, de contexto e de componente. Sem E2E neste projeto.

### III. Camadas (POSA) e um caso de uso por Service
Backend: apresentação (Controller/Request/Resource) → aplicação (Service) → persistência (Repository). O controller não contém regra de negócio e não chama Eloquent. Frontend: apresentação → estado (Context) → acesso à API. A tela não faz `fetch`. Caso de uso novo vira peça nova, sem inchar a que já existe.

### IV. Docker é o ambiente
PHP, Node e banco não se instalam na máquina host. Comandos de teste, migrate e execução passam por `docker compose` na pasta do laboratório.

### V. Identidade e dados de quem usa
Tarefas pertencem a quem autenticou. Um usuário não lê nem altera a lista de outro. Segredos de provedores (Google, GitHub) ficam só em variáveis de ambiente, nunca no repositório. O provedor de identidade fica atrás de uma abstração, para a regra de negócio e os testes não falarem com a internet.

## Restrições de produto e de segurança

- Layout moderno com MUI; não entregar tela crua.
- Não versionar `.env`, credenciais nem pastas de dependência.
- Não pular camada. Não aplicar padrão GoF por aplicar.
- Migração que muda dono de dado (ex.: `user_id` em tarefas) é checkpoint humano obrigatório: a execução para, a pessoa revisa e decide aprovar, editar ou voltar à spec.

## Fluxo de trabalho com agentes

1. Comportamento (user story) sem decisão técnica.
2. Requisitos e critérios de aceite testáveis, com pelo menos um caso de borda.
3. Plano de tarefas revisado por um humano.
4. Implementação tarefa a tarefa, com revisão do diff.
5. Verificação pelos testes derivados da spec.

## Governance

Esta constituição prevalece sobre o hábito do modelo de “fazer o caminho mais curto”. Emenda exige registro (data e motivo) neste arquivo. O `AGENTS.md` e as regras em `.cursor/rules/` traduzem estes princípios para o dia a dia do agente.

**Version**: 1.0.0 | **Ratified**: 2026-08-31 | **Last Amended**: 2026-08-31

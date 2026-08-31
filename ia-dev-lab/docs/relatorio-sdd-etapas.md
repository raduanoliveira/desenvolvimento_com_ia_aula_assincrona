# Diário das etapas da prática SDD

Este arquivo guarda o texto completo que eu escrevi no relatório da atividade. O PDF de entrega tem só uma página, como pede o enunciado. O professor encontra aqui o detalhe de cada etapa.

Repositório: https://github.com/raduanoliveira/desenvolvimento_com_ia_aula_assincrona.git

## Etapa 1. Escolha do projeto e da funcionalidade

Eu resolvi usar o mesmo projeto da aula assíncrona passada: o ToDo em `ia-dev-lab`, com API Laravel, tela React, Docker, `AGENTS.md` e TDD. Quis deixar clara a evolução da ferramenta de acordo com o que foi ensinado em aula: na prática anterior configurei o ambiente e pratiquei prompt eficaz; nesta pratiquei Spec-Driven Development com Spec Kit e OpenSpec, em cima do mesmo laboratório.

As duas funcionalidades foram:

1. Entrar com Google, para a pessoa ver só as próprias tarefas.
2. Entrar com GitHub, com a mesma ideia e outro provedor.

Por que são boas para SDD: têm regra de negócio (dono), vários arquivos e casos de borda (cancelar no provedor, pedido sem sessão, isolamento entre contas, tarefas antigas sem dono). Sem spec, a IA tende a deixar a lista global e a regra no controller.

Entregáveis: `docs/escopo.md` e `docs/sdd-ferramentas.md`. O prompt de comportamento do Google está em `docs/sdd-prompt-google.md`.

## Etapa 2. Especificação com Spec Kit (login Google)

Eu escolhi o GitHub Spec Kit na primeira feature porque o fluxo começa pela constituição do projeto e cria o domínio de identidade do zero.

O que eu rodei no Cursor:

1. `/speckit-specify` gerou `specs/001-google-sign-in/spec.md` (user stories, Given/When/Then, requisitos). Sem código.
2. Eu revisei a spec e aprovei. Não precisei do `/speckit-clarify`.
3. `/speckit-plan` gerou `plan.md`, `research.md`, `data-model.md`, `contracts/` e `quickstart.md`.
4. `/speckit-tasks` gerou `tasks.md` com 70 tarefas. O checkpoint humano ficou na tarefa T030.
5. Eu revisei o plano de tarefas e aprovei a ordem: identidade primeiro, parar no T030, depois dono das tarefas, depois UI.

## Etapa 3. Execução

Eu pedi ao agente `/speckit-implement` em fatias, não tudo de uma vez.

- T001 a T029: identidade, tabela `users`, sessão e OAuth Google (fake nos testes).
- T031 a T046: depois do checkpoint, dono nas tarefas, 401 sem sessão, isolamento entre contas e órfãs.
- T047 a T070: tela de entrada, lista pessoal, sair e cancelar no Google.

Os testes no Docker passaram (frontend e backend). Eu validei no browser com duas contas Google.

**Diff que eu revisei.** No repositório de tarefas, `allForUser` e `findForUser` isolam por dono, mas o método `all()` legado ainda devolve todas as tarefas. Sem essa revisão eu teria aceitado o caminho feliz e deixado um atalho perigoso. O fluxo atual da API não chama `all()`.

## Etapa 4. Checkpoint humano

O checkpoint obrigatório foi a tarefa T030: parar antes da migration que adiciona `user_id` em `tasks`.

Eu parei a execução, li o `quickstart.md` e o `data-model.md`, e aprovei: coluna nullable, sem backfill e sem `NOT NULL`. Papel humano: decidir o impacto nas tarefas antigas sem dono, não só deixar o agente seguir.

## Etapa 5. OpenSpec (login GitHub)

Ferramenta da segunda feature: OpenSpec, porque a mudança é um delta em brownfield (mais um provedor sobre o que o Spec Kit já criou).

O que eu rodei: `/opsx-propose login-com-github` (proposal, delta spec, design, tasks) e depois `/opsx-apply`. Artefatos em `openspec/changes/login-com-github/`.

Implementação: Continuar com GitHub na tela, `CompleteGitHubSignInService` (não une por e-mail), `github_id` nullable, Google intacto. Testes: backend 49 e frontend 23 no momento da auditoria. Validei no browser.

**Comparação rápida com Spec Kit.** Spec Kit gerou pasta `specs/001-...` com constitution/plan e ~70 tarefas; OpenSpec gerou delta em `openspec/changes/` com tasks mais curtas. Os dois respeitaram o `AGENTS.md` (ver `docs/conformidade-agents.md`).

**Diff que eu revisei (GitHub).** O Service só chama `findByGithubId`; não busca por e-mail. Sem essa leitura, unir contas pelo mesmo e-mail seria o caminho mais curto e quebraria a spec.

## Resultados dos testes (Spec Kit e OpenSpec)

Em 31/08/2026 eu rodei de novo a suíte no Docker e o smoke HTTP. Registro completo: `docs/resultados-testes-sdd.md`.

- Backend: **49 passed** (181 assertions). Frontend: **23 passed**.
- Sem sessão: `/api/session` e `/api/tasks` → **401**.
- `/auth/google` e `/auth/github` → **302** para o provedor certo, com callback alinhado ao contrato.
- Browser: duas contas Google (Spec Kit) e login GitHub (OpenSpec) ok; os dois botões na tela de entrada.

Os testes usam fake (sem internet). O caminho feliz OAuth real usa as chaves só no `.env` local.

## Conformidade com AGENTS.md

Auditoria das duas implementações: `docs/conformidade-agents.md`.

Veredito: Google (Spec Kit) e GitHub (OpenSpec) seguiram TDD, regra no Service, Repository, POSA, SOLID (caso de uso novo = peça nova), frontend Context/MUI sem `fetch` na tela, Docker e sem segredo no git. Ressalva: método `all()` legado no repositório de tarefas.

## Etapa 6. Git e GitHub

Histórico real de commits do processo e Pull Request aberto para a `main`.

(Atualizar com a URL do PR ao publicar.)

## Etapa 7. Página única

O PDF `relatorio-sdd.pdf` na raiz cobre os três pontos do roteiro, com um resumo da tabela Spec Kit × OpenSpec. A tabela completa (com coluna Preferência) está em `docs/comparativo-speckit-openspec.md`. Os resultados da bateria de testes (automatizados, smoke HTTP e browser) estão em `docs/resultados-testes-sdd.md`. A dificuldade que eu relato na página única é manter o controle humano no SDD (checkpoint T030 e revisão do diff do `all()` legado). A conformidade com o `AGENTS.md` está detalhada em `docs/conformidade-agents.md`. Este arquivo guarda o restante.

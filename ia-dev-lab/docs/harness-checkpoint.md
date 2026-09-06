# Etapa 3. Checkpoint humano

Prática: Harness e Arquitetura. Laboratório: `ia-dev-lab`.

## Checkpoint definido

Parar **antes de criar ou aplicar uma migration**.

Isso cobre:
- criar arquivo em `backend/database/migrations/`
- rodar `php artisan migrate` (também via `docker compose exec backend ...`)

Não cobre o que o hook da Etapa 1 já bloqueia (`migrate:fresh`, `migrate:refresh`, `migrate:reset`, `db:wipe`, `schema:drop`, apagar `database.sqlite`). O hook impede destruir o banco. Este checkpoint decide se o schema **pode mudar**.

A regra ficou no `AGENTS.md` e na rule `checkpoint-migration.mdc`, para o agente parar de novo nas próximas etapas.

## Simulação

Data: 6 de setembro de 2026. Branch: `feature/prazo-plan`. Modo: Plan.

O agente montou o plano da feature de prazo (`due_date`) com “migration nova” e **não aplicou** o schema enquanto eu não aceitei o plano. A execução parou nesse ponto.

Migration em jogo: `2026_09_06_120000_add_due_date_to_tasks_table` (coluna opcional `due_date` em `tasks`).

## Decisão

**Aprovar.**

Autorizei o plano. Só então a implementação e o `migrate` seguiram. Não editei o recorte da coluna (opcional, só data, `Y-m-d`). Não rejeitei.

Houve uma segunda parada do mesmo tipo na prioridade (`2026_09_06_141500_add_priority_to_tasks_table`): o Superpowers apresentou o desenho com coluna no banco e só implementou depois da minha aprovação. Também **aprovar**.

## Papel humano

Assumi o papel de **responsável pelo schema e pelos dados do laboratório**, não só de revisor de código.

Justificativa: o SQLite do Docker é o estado real das tarefas. Uma migration aplicada entra no banco compartilhado e no histórico Git. O hook não pergunta se a coluna nova é desejável; ele só recusa wipe. Sem este checkpoint, o agente no Auto da Etapa 1 criaria e aplicaria a migration no mesmo fluxo contínuo. No Plan, a parada existiu de fato: plano escrito, aceite, depois código.

## Evidência

- Transcript: `docs/sessao-log.md` (início da execução Plan e aceite do desenho da prioridade).
- Plano da Etapa 1: `docs/harness-execucao-plan.md`.
- Status atual: as duas migrations constam como Ran no `php artisan migrate:status`.

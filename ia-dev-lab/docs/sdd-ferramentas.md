# Ferramentas de SDD usadas nesta prática

Escolhi as duas ferramentas open-source vistas na Aula 3, as duas no repositório, sem Traycer.ai (não tenho acesso).

## Spec Kit (GitHub) — login com Google

Usei o Spec Kit na primeira feature porque o fluxo começa pela **constituição**: princípios que valem para o projeto inteiro (TDD, camadas, Docker, não versionar segredo). Login com Google introduz identidade e dono da tarefa; esses princípios precisam existir antes do plano técnico. Os artefatos ficam em `specs/<feature>/` (`spec.md`, `plan.md`, `tasks.md`).

Comandos no Cursor (depois do `specify init --integration cursor-agent`): `/speckit-constitution`, `/speckit-specify`, `/speckit-plan`, `/speckit-tasks`, `/speckit-implement`.

## OpenSpec — login com GitHub

Usei o OpenSpec na segunda feature porque ele é feito para **brownfield** e **delta spec** (ADDED / MODIFIED / REMOVED). Depois do Google, o sistema já tem usuário, sessão e tarefa com dono. GitHub só acrescenta um provedor. Os artefatos ficam em `openspec/changes/<change>/` (`proposal.md`, `specs/`, `design.md`, `tasks.md`).

Comandos no Cursor (depois do `openspec init --tools cursor`): `/opsx-propose`, `/opsx-apply`, `/opsx-archive`.

## O que eu não usei

Traycer.ai: o enunciado permite, mas a conta não faz parte deste laboratório. Markdown manual também seria válido; preferi as duas ferramentas da aula para cumprir a Etapa 5 (comparar estratégias).

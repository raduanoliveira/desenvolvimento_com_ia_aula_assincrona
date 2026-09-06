# Harness Etapa 1. Comparação Auto vs Plan

Data: 2026-09-06  
Projeto: `ia-dev-lab`  
Feature única (mesmo prompt): prazo opcional (`due_date`) + lembrete toaster no login para tarefas ativas com prazo amanhã.  
Modelo nas duas execuções: Composer.

Registros detalhados:

- Auto: `docs/harness-execucao-auto.md` (branch `feature/prazo-auto`)
- Plan: `docs/harness-execucao-plan.md` (branch `feature/prazo-plan`)

## Tabela-resumo

| Dimensão | Auto | Plan | Preferência |
| --- | --- | --- | --- |
| Tempo | ~30 min ponta a ponta | ~40 min após ok do plano (+ tempo de planejar/revisar) | Auto (mais rápido) |
| Controle | média | alta | Plan |
| Risco | médio | baixo | Plan |
| Qualidade / testes | 54 backend, 25 frontend | 54 backend, 26 frontend | Plan (ligeira) |
| Conformidade AGENTS.md | Service + TDD, com correções depois | Service + TDD, ordem explícita no plano | Plan |

## O que foi igual

- Mesmo prompt.
- Mesma arquitetura: migration, `CreateTaskService`, `ListDueTomorrowRemindersService`, controller magro, rota autenticada, Context + toaster MUI.
- ADRs 0001–0004 intactos.
- Comportamento funcional equivalente.

## O que diferiu

- **Auto** avançou sem plano escrito revisado; eu só olhei o resultado. Houve retrabalho curto nos testes (Carbon e mocks do toaster).
- **Plan** obrigou a escrever e aceitar o plano antes de editar. Demorou mais, mas a ordem Red-Green ficou clara e o risco de “aceitar tudo” caiu.
- Nomes de migration diferentes (`000001` na Auto, `120000` na Plan); o banco local já tinha `due_date` quando rodei `migrate` na Plan (`Nothing to migrate`, coluna presente).

## Decisão

Fico com **`feature/prazo-plan`** como branch vencedora para seguir o harness (merge futuro / base das próximas etapas). Motivo: controle e risco melhores sem perda de qualidade. A `feature/prazo-auto` permanece no repositório como evidência da comparação.

## Pendência da Etapa 1 (enunciado)

Ainda falta o **hook** que bloqueia uma ação de risco do projeto (diferente de “bloquear merge na main”), com evidência de bloqueio. Isso não entra neste comparativo de autonomia.

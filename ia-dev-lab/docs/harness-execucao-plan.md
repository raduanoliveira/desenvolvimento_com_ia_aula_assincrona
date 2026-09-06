# Harness Etapa 1. Execução Plan

Branch: feature/prazo-plan
Modo de autonomia: Plan (planejar, revisar, só então editar)
Data: 2026-09-06

## Modelo usado

Cursor Plan / Agent após ok do plano. Modelo desta sessão: Composer.

## Prompt enviado (idêntico ao da execução Auto)

Contexto: este é o ia-dev-lab, ToDo em monorepo. A API é Laravel com autenticação (Google e GitHub), Service, Repository e testes. A tela é React com TypeScript, Context API e MUI. O ambiente sobe com Docker. Siga o AGENTS.md: TDD, um caso de uso por Service, controller só HTTP, persistência por interface de repositório, a tela não chama a API direto.

Tarefa: implementar prazo (due_date) nas tarefas e lembrete no sistema.

Detalhe do prazo:
- Campo opcional due_date (só data, formato Y-m-d).
- Migration nova.
- Aceitar due_date na criação (validação na Request, regra no CreateTaskService).
- Devolver due_date no TaskResource.
- No formulário, campo de data opcional; na lista, mostrar o prazo.

Detalhe do lembrete:
- Só no sistema (toaster), sem e-mail e sem push.
- Ao carregar a sessão autenticada (login / entrada no dashboard), avisar tarefas ativas, não arquivadas, não concluídas, cujo due_date é amanhã (faltando 1 dia).
- Um Service novo para listar esses lembretes; endpoint autenticado; toaster MUI na tela.

Restrições: não colocar regra de negócio no controller nem na tela. Não pular camada. Não alterar os ADRs 0001 a 0004. Não instalar PHP ou Node no Windows. Rodar testes no Docker.

Validação: testes de unidade e de feature no backend; testes de API, formulário, lista e toaster no frontend.

## Plano construído (antes da implementação)

### Preparação

1. Trabalho Auto permanece em feature/prazo-auto (stash wip-prazo-auto).
2. Branch feature/prazo-plan a partir de feature/setup-inicial.
3. Containers do ia-dev-lab no ar.

### Escopo

- Prazo: migration due_date; StoreTaskRequest; CreateTaskService; TaskResource; Task model/factory; TaskForm; TaskList.
- Lembrete: ListDueTomorrowRemindersService; GET /api/reminders/due-tomorrow; toaster no dashboard ao entrar autenticado.

### Ordem TDD

Backend: teste CreateTaskService -> implementação create/migration -> teste ListDueTomorrowRemindersService -> service/controller/rota -> feature tests -> php artisan test.

Frontend: types e taskApi -> testes (api, form, lista, toaster, mocks) -> Context/form/lista/toaster -> npm test.

### Critérios de pronto

Mesmo comportamento do prompt; testes passando no Docker; este arquivo com prompt e plano; sem commit sem autorização; ADRs antigos intactos.

## Registro para a comparação (preencher ao fechar)

- Tempo gasto (aproximado): ~40 min de implementação após o ok do plano (branch + TDD backend/frontend + testes Docker), além do tempo prévio de montar e revisar o plano.
- Sensação de controle (alta / média / baixa): alta (plano revisado antes de editar; ordem TDD explícita; escopo fechado).
- Risco percebido (alto / médio / baixo): baixo (mesma feature do Auto, testes 54 backend / 26 frontend passando no Docker antes de qualquer commit).
- Observações: branch feature/prazo-plan limpa a partir de feature/setup-inicial; Auto permanece no stash wip-prazo-auto em feature/prazo-auto. Sem commit nesta execução. Migration local do lab ainda depende de autorização explícita (`php artisan migrate`). ADRs 0001–0004 intactos.

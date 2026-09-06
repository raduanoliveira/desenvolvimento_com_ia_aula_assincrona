# Harness Etapa 1. Execução Auto

Branch: feature/prazo-auto
Modo de autonomia: Auto (aceite automático de edições / agente executa com menos paradas)
Data: 2026-09-06

## Modelo usado

Cursor Auto (agente no editor). Modelo desta sessão: Composer.

Se no seletor do Cursor você usou outro modelo nesta execução, anote aqui e corrija esta linha.

## Prompt enviado (mesmo texto que será repetido no modo Plan)

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

## O que foi feito nesta execução

- Branch feature/prazo-auto criada a partir de feature/setup-inicial.
- Migration due_date, CreateTaskService, ListDueTomorrowRemindersService, TaskReminderController, rota GET /api/reminders/due-tomorrow.
- Frontend: campo Prazo, chip na lista, DueRemindersToaster no dashboard.
- Testes backend e frontend atualizados.

## Registro para a comparação (preencher ao fechar a execução)

- Tempo gasto (aproximado): ~30 min de ponta a ponta (implementação contínua + correção de 3 testes no Docker).
- Sensação de controle (alta / média / baixa): média (agente avançou sem plano revisado; eu só acompanhei o resultado e os testes).
- Risco percebido (alto / médio / baixo): médio (edições aceitas em sequência; precisei corrigir asserção de Carbon e mocks do toaster depois).
- Observações: resultado funcional equivalente ao Plan (54 backend / 25 frontend na bateria final da Auto). Sem commit até autorização. ADRs 0001–0004 intactos.

## Próximo passo

Repetir o MESMO prompt acima na branch feature/prazo-plan, no modo Plan, com o mesmo modelo ou registrando o modelo se for outro. Depois comparar e escolher qual branch fica.

# Etapa 2. Tarefa B (sem TDD): editar título

Branch: `feature/tdd-guardrail`  
Data: 2026-09-06  
Pedido deliberado ao agente: implementar **sem escrever teste antes**.

## O que foi entregue

- Backend: `UpdateTaskTitleService`, `UpdateTaskTitleRequest`, `PATCH /api/tasks/{task}`, método magro no controller.
- Frontend: `updateTaskTitle` na API, `renameTask` no Context, botão Editar e campo Salvar na `TaskList`.
- Validação de título obrigatório ficou na Request (por hábito do projeto), mas **não houve Red** observado.

## O que faltou por pular o TDD

- Nenhum teste de unidade do Service (dono vs não dono, trim do título).
- Nenhum teste de feature da API (sucesso, 422 para título vazio, isolamento entre usuários).
- Nenhum teste de `taskApi.updateTaskTitle` nem do fluxo Editar/Salvar na lista.
- A bateria existente (58 backend / 28 frontend) continua verde, o que **mascara** a ausência de cobertura da feature nova: os testes antigos não exercitam o rename.

## Sensação

Foi mais rápido digitar o caminho feliz. A confiança, porém, é menor: bordas óbvias (título vazio já rejeitado pela Request, mas sem prova automatizada desta rota; edição por outro usuário) não foram forçadas a falhar antes do código.

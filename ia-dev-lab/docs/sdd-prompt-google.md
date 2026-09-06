# Spec em comportamento — entrar com Google

Este arquivo é o passo humano da Etapa 2, **antes** do Spec Kit gerar `spec.md` / `plan.md` / `tasks.md`. Só comportamento. Nenhuma biblioteca, rota ou banco.

## Prompt inicial (user story)

Como pessoa que usa a lista de tarefas, quero entrar com a minha conta Google, para que só eu veja e altere as minhas tarefas.

## Requisitos (PRD)

1. Quem não autenticou não vê a lista nem o formulário de nova tarefa. Vê uma tela de entrada com a ação de continuar com Google.
2. Depois de autenticar com Google com sucesso, a pessoa volta ao aplicativo já identificada e vê só as tarefas dela.
3. Criar, concluir, arquivar e excluir tarefa só vale para as tarefas de quem está autenticado.
4. Um pedido à API sem sessão válida é recusado. Não devolve lista de outra pessoa.
5. A pessoa pode encerrar a sessão. Depois disso, volta à tela de entrada.
6. Se a pessoa cancelar ou recusar o consentimento no Google, permanece desconectada e vê uma mensagem clara. A lista não abre.
7. Tarefas que já existiam sem dono deixam de aparecer para qualquer pessoa autenticada. Não são “herdadas” pelo primeiro que entrar.
8. Segredos do aplicativo Google não aparecem na tela, no repositório nem em mensagem de erro.

## Critérios de aceite (Given / When / Then)

### Cenário 1 — caminho feliz

Dado que eu não estou autenticado  
Quando eu continuo com Google e o Google confirma a minha identidade  
Então eu vejo a lista só com as minhas tarefas (vazia, se eu nunca criei nenhuma) e posso criar uma nova

### Cenário 2 — isolamento entre pessoas

Dado que a pessoa A está autenticada e criou a tarefa “Pagar conta”  
Quando a pessoa B autentica com outra conta Google  
Então B não vê “Pagar conta” e não consegue concluir, arquivar nem excluir essa tarefa

### Cenário 3 — caso de borda: cancelar no Google

Dado que eu estou na tela de entrada  
Quando eu continuo com Google e cancelo ou recuso o consentimento  
Então eu permaneço desconectado, vejo uma mensagem de que a entrada não foi concluída, e a lista de tarefas não aparece

### Cenário 4 — caso de borda: API sem sessão

Dado que não há sessão válida  
Quando um cliente pede a listagem ou tenta criar uma tarefa  
Então o sistema recusa o pedido e não devolve tarefa de ninguém

## Fora desta feature

- Entrar com GitHub (feature seguinte, OpenSpec).
- Unir a conta Google com outra identidade.
- Permanecer na lista anônima antiga.

## Checkpoint humano previsto

Antes de aplicar a migração que passa a exigir dono nas tarefas: parar, revisar o impacto nas tarefas sem dono, e só então aprovar, editar ou voltar à spec.

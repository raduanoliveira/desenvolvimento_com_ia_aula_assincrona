## Purpose

Permite entrar com a conta GitHub para ver e alterar só as próprias tarefas, como identidade separada da conta Google, sem unir as duas.

## ADDED Requirements

### Requirement: Tela de entrada oferece GitHub junto com Google

O sistema SHALL mostrar, a quem não autenticou, a tela de entrada com a ação **Continuar com GitHub** visível junto com **Continuar com Google**. A lista de tarefas e o formulário de nova tarefa SHALL NOT aparecer enquanto não houver sessão válida.

#### Scenario: Visitante vê as duas ações

- **WHEN** uma pessoa abre o aplicativo sem sessão válida
- **THEN** ela vê a tela de entrada com Continuar com Google e Continuar com GitHub, e não vê a lista nem o formulário de nova tarefa

#### Scenario: GitHub não abre a lista antes de concluir

- **WHEN** a pessoa está na tela de entrada e ainda não concluiu a entrada com GitHub
- **THEN** a lista de tarefas não abre

### Requirement: Entrar com GitHub devolve a pessoa identificada à lista dela

O sistema SHALL permitir que a pessoa continue com a conta GitHub e, quando o GitHub confirmar a identidade, SHALL devolvê-la ao aplicativo já identificada. Depois de autenticada, ela SHALL ver somente as tarefas das quais é dona. Se não houver nenhuma, a lista SHALL aparecer vazia. Enquanto autenticada, ela SHALL conseguir identificar quem está na sessão (por nome ou e-mail obtidos do GitHub).

#### Scenario: Primeira entrada com GitHub

- **WHEN** a pessoa continua com GitHub e o GitHub confirma a identidade
- **THEN** ela vê a lista só com as próprias tarefas (vazia, se nunca criou nenhuma) e pode criar uma nova

#### Scenario: Mesma conta GitHub em visita posterior

- **WHEN** a pessoa já autenticou com GitHub, criou tarefas, e entra de novo com a mesma conta GitHub
- **THEN** ela vê as mesmas tarefas dela e nenhuma tarefa de outra pessoa

#### Scenario: Sessão identificada

- **WHEN** a pessoa autenticou com GitHub com sucesso e olha a tela da lista
- **THEN** fica claro quem está na sessão e ela não vê a tela de entrada

### Requirement: Conta Google e conta GitHub não se unem

O sistema SHALL tratar conta Google e conta GitHub como identidades distintas. Mesmo quando o e-mail coincidir, SHALL NOT unir as contas, SHALL NOT transferir tarefas entre elas e SHALL manter listas separadas.

#### Scenario: Mesmo e-mail em provedores diferentes

- **WHEN** uma pessoa entra com Google, cria tarefas, sai, e depois entra com GitHub usando o mesmo endereço de e-mail
- **THEN** a sessão GitHub vê uma lista distinta (vazia, se essa conta GitHub nunca criou tarefas) e não vê as tarefas da conta Google

#### Scenario: Voltar ao Google não herda o GitHub

- **WHEN** a mesma pessoa, depois de usar GitHub, entra de novo com a conta Google anterior
- **THEN** ela reencontra só as tarefas da conta Google e não as da conta GitHub

### Requirement: Criar e alterar só as tarefas da conta autenticada

Criar, concluir, arquivar e excluir tarefa SHALL valer apenas para tarefas de quem está autenticado. O sistema SHALL recusar qualquer tentativa de concluir, arquivar ou excluir tarefa de outra pessoa, inclusive quando a outra pessoa autenticou com o outro provedor.

#### Scenario: Isolamento entre duas contas GitHub

- **WHEN** a pessoa A autenticada com GitHub criou uma tarefa e a pessoa B autentica com outra conta GitHub
- **THEN** B não vê a tarefa de A e não consegue concluí-la, arquivá-la nem excluí-la

#### Scenario: Isolamento entre Google e GitHub

- **WHEN** a pessoa A autenticada com Google criou uma tarefa e a pessoa B autentica com GitHub
- **THEN** B não vê a tarefa de A e, mesmo conhecendo a identificação da tarefa, o sistema recusa alteração e a tarefa de A permanece inalterada

#### Scenario: Dona GitHub altera só as dela

- **WHEN** a pessoa autenticada com GitHub cria, conclui, arquiva ou exclui uma tarefa dela
- **THEN** a operação vale só para essa tarefa e a lista dela reflete a mudança

### Requirement: Pedido sem sessão é recusado

Qualquer pedido ao sistema para listar, criar, concluir, arquivar ou excluir tarefa, feito sem sessão válida, SHALL ser recusado e SHALL NOT devolver tarefa de ninguém. O mesmo vale para pedido de sessão: sem sessão válida o sistema SHALL recusar e SHALL NOT devolver dados de pessoa autenticada.

#### Scenario: Listagem sem sessão

- **WHEN** um cliente pede a listagem de tarefas sem sessão válida
- **THEN** o sistema recusa o pedido (401) e não devolve tarefa de ninguém

#### Scenario: Mutação sem sessão

- **WHEN** um cliente tenta criar, concluir, arquivar ou excluir uma tarefa sem sessão válida
- **THEN** o sistema recusa o pedido e nenhuma tarefa é criada nem alterada

### Requirement: Cancelar ou falhar no GitHub permanece desconectado

Se a pessoa cancelar ou recusar o consentimento no GitHub, o sistema SHALL mantê-la desconectada, SHALL mostrar mensagem clara de que a entrada não foi concluída, e SHALL NOT abrir a lista. Se o GitHub não confirmar a identidade por erro ou indisponibilidade, o sistema SHALL manter o mesmo comportamento de falha com mensagem clara. As ações Continuar com Google e Continuar com GitHub SHALL permanecer disponíveis para tentar de novo.

#### Scenario: Cancelamento no GitHub

- **WHEN** a pessoa continua com GitHub e cancela ou recusa o consentimento
- **THEN** ela permanece desconectada, vê uma mensagem de que a entrada não foi concluída, e a lista de tarefas não aparece

#### Scenario: Erro ou indisponibilidade do GitHub

- **WHEN** o GitHub não confirma a identidade por erro ou indisponibilidade
- **THEN** a pessoa permanece desconectada, vê uma mensagem clara, a lista não abre, e nenhum segredo é exibido

#### Scenario: Tentar de novo depois de cancelar

- **WHEN** a entrada com GitHub não foi concluída e a pessoa olha a tela
- **THEN** Continuar com Google e Continuar com GitHub continuam disponíveis

### Requirement: Segredos do aplicativo GitHub ficam fora da superfície

Segredos do aplicativo GitHub (`GITHUB_CLIENT_ID`, `GITHUB_CLIENT_SECRET` e URI de callback) SHALL existir só em variáveis de ambiente. SHALL NOT aparecer na tela, no repositório do projeto nem em mensagem de erro.

#### Scenario: Revisão da superfície

- **WHEN** alguém revisa a tela, as mensagens de erro e o repositório versionado
- **THEN** não encontra `GITHUB_CLIENT_ID`, `GITHUB_CLIENT_SECRET` nem valores secretos de callback

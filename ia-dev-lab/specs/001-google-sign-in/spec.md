# Feature Specification: Entrar com Google

**Feature Branch**: `001-google-sign-in`

**Created**: 2026-08-31

**Status**: Draft

**Input**: User description: "Como pessoa que usa a lista de tarefas, quero entrar com a minha conta Google, para que só eu veja e altere as minhas tarefas. Quem não autenticou não vê a lista. Vê uma tela de entrada com continuar com Google. Depois de autenticar, vê só as próprias tarefas. Criar, concluir, arquivar e excluir só nas dela. Pedido à API sem sessão é recusado. Pode encerrar a sessão e volta à tela de entrada. Se cancelar no Google, permanece desconectado e vê mensagem clara. Tarefas antigas sem dono não são herdadas por quem entrar primeiro. Segredos do Google não aparecem na tela nem no repositório. Não implementar código. Só gerar a especificação."

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Entrar com Google e ver só as próprias tarefas (Priority: P1)

Uma pessoa que já usa (ou vai usar) a lista de tarefas escolhe continuar com a conta Google. O Google confirma a identidade. Ela volta ao aplicativo já identificada e vê somente as tarefas que ela mesma criou. Se nunca criou nenhuma, a lista aparece vazia e ela pode criar a primeira.

**Why this priority**: É o valor central da feature — identidade reconhecida e lista pessoal. Sem isso, o restante não entrega o pedido.

**Independent Test**: Completar a entrada com Google com sucesso e verificar que a lista exibida contém apenas tarefas daquela pessoa (ou está vazia) e que uma nova tarefa criada passa a aparecer para ela.

**Acceptance Scenarios**:

1. **Given** que a pessoa não está autenticada, **When** ela continua com Google e o Google confirma a identidade, **Then** ela vê a lista só com as próprias tarefas (vazia, se nunca criou nenhuma) e pode criar uma nova.
2. **Given** que a pessoa já autenticou com Google e criou tarefas numa visita anterior, **When** ela entra de novo com a mesma conta Google, **Then** ela vê as mesmas tarefas dela e nenhuma tarefa de outra pessoa.
3. **Given** que a pessoa autenticou com sucesso, **When** ela olha a tela da lista, **Then** fica claro que ela está identificada (vê quem está na sessão) e não vê a tela de entrada.

---

### User Story 2 - Visitante desconectado vê só a tela de entrada (Priority: P1)

Quem abre o aplicativo sem sessão válida não vê a lista de tarefas nem o formulário de nova tarefa. Vê uma tela de entrada cuja ação principal é continuar com Google.

**Why this priority**: Sem este bloqueio, a lista continua exposta a qualquer visitante e o isolamento por dono não existe na prática.

**Independent Test**: Abrir o aplicativo sem sessão e confirmar que só a tela de entrada aparece, com a ação de continuar com Google, sem lista e sem formulário de nova tarefa.

**Acceptance Scenarios**:

1. **Given** que não há sessão válida, **When** a pessoa abre o aplicativo, **Then** ela vê a tela de entrada com continuar com Google e não vê a lista nem o formulário de nova tarefa.
2. **Given** que a pessoa está na tela de entrada, **When** ela ainda não concluiu a entrada com Google, **Then** a lista de tarefas não abre.

---

### User Story 3 - Criar, concluir, arquivar e excluir só nas próprias tarefas (Priority: P1)

Depois de autenticada, a pessoa cria, conclui, arquiva e exclui apenas tarefas dela. Outra pessoa autenticada com outra conta Google não vê essas tarefas e não consegue alterá-las.

**Why this priority**: O pedido é ver **e alterar** só as próprias tarefas. Isolamento de leitura sem isolamento de alteração deixaria a lista pessoal insegura.

**Independent Test**: Com duas pessoas (contas Google distintas), a pessoa A cria uma tarefa; a pessoa B autentica e tenta ver, concluir, arquivar ou excluir essa tarefa — B não vê e não consegue alterar.

**Acceptance Scenarios**:

1. **Given** que a pessoa A está autenticada e criou a tarefa “Pagar conta”, **When** a pessoa B autentica com outra conta Google, **Then** B não vê “Pagar conta” e não consegue concluir, arquivar nem excluir essa tarefa.
2. **Given** que a pessoa A está autenticada, **When** ela cria, conclui, arquiva ou exclui uma tarefa dela, **Then** a operação vale só para essa tarefa e a lista de A reflete a mudança.
3. **Given** que a pessoa B está autenticada, **When** B tenta concluir, arquivar ou excluir uma tarefa que pertence a A (mesmo conhecendo a identificação da tarefa), **Then** o sistema recusa a operação e a tarefa de A permanece inalterada.

---

### User Story 4 - Pedido sem sessão é recusado (Priority: P2)

Qualquer pedido ao sistema de listar, criar, concluir, arquivar ou excluir tarefa, feito sem sessão válida, é recusado. O sistema não devolve a lista de ninguém.

**Why this priority**: A tela de entrada protege a interface; este cenário protege o acesso direto ao sistema. Sem isso, a lista ainda poderia vazar por fora da tela.

**Independent Test**: Sem sessão válida, pedir a listagem e tentar criar uma tarefa; o sistema recusa e não devolve tarefa de ninguém.

**Acceptance Scenarios**:

1. **Given** que não há sessão válida, **When** um cliente pede a listagem de tarefas, **Then** o sistema recusa o pedido e não devolve tarefa de ninguém.
2. **Given** que não há sessão válida, **When** um cliente tenta criar, concluir, arquivar ou excluir uma tarefa, **Then** o sistema recusa o pedido e nenhuma tarefa é criada nem alterada.

---

### User Story 5 - Encerrar a sessão e voltar à tela de entrada (Priority: P2)

A pessoa autenticada pode encerrar a sessão. Depois disso, volta à tela de entrada e deixa de ver a lista até autenticar de novo.

**Why this priority**: Fecha o ciclo da sessão. Sem saída explícita, a pessoa não controla quem continua identificado naquele aparelho.

**Independent Test**: Autenticar, encerrar a sessão e confirmar o retorno à tela de entrada, sem lista visível.

**Acceptance Scenarios**:

1. **Given** que a pessoa está autenticada e vê a lista dela, **When** ela encerra a sessão, **Then** ela volta à tela de entrada e a lista não aparece.
2. **Given** que a pessoa encerrou a sessão, **When** ela tenta usar o aplicativo de novo sem autenticar, **Then** permanece desconectada e qualquer pedido ao sistema sem sessão é recusado (como na User Story 4).

---

### User Story 6 - Cancelar no Google permanece desconectado (Priority: P3)

Se a pessoa inicia continuar com Google e cancela ou recusa o consentimento no Google, ela permanece desconectada, vê uma mensagem clara de que a entrada não foi concluída, e a lista não abre.

**Why this priority**: Caso de borda obrigatório do fluxo com provedor externo. Não bloqueia o caminho feliz, mas evita deixar a pessoa numa tela em branco ou, pior, autenticada sem consentimento.

**Independent Test**: Na tela de entrada, continuar com Google e cancelar ou recusar o consentimento; verificar mensagem clara, ausência de lista e ausência de sessão.

**Acceptance Scenarios**:

1. **Given** que a pessoa está na tela de entrada, **When** ela continua com Google e cancela ou recusa o consentimento, **Then** ela permanece desconectada, vê uma mensagem de que a entrada não foi concluída, e a lista de tarefas não aparece.
2. **Given** que a entrada com Google não foi concluída, **When** a pessoa olha a tela, **Then** a ação de continuar com Google continua disponível para tentar de novo.

---

### User Story 7 - Tarefas antigas sem dono não são herdadas (Priority: P3)

Tarefas que já existiam sem dono deixam de aparecer para qualquer pessoa autenticada. Não são atribuídas à primeira pessoa que entrar.

**Why this priority**: Evita que dados anônimos antigos virem lista pessoal de quem autenticar primeiro. É regra de fronteira desta feature, não o caminho diário.

**Independent Test**: Existindo tarefas sem dono, autenticar com Google e confirmar que essas tarefas não aparecem na lista da pessoa nem na de outra conta.

**Acceptance Scenarios**:

1. **Given** que existem tarefas criadas antes desta feature, sem dono, **When** qualquer pessoa autentica com Google, **Then** essas tarefas não aparecem na lista dela.
2. **Given** que existem tarefas sem dono, **When** a primeira pessoa autentica e cria as próprias tarefas, **Then** as tarefas sem dono não passam a pertencer a ela e continuam invisíveis para todas as pessoas autenticadas.

---

### Edge Cases

- A pessoa cancela ou recusa o consentimento no Google: permanece desconectada, vê mensagem clara, lista não abre.
- Pedido ao sistema sem sessão válida: recusado; não devolve tarefa de ninguém.
- Pessoa B tenta alterar tarefa da pessoa A conhecendo a identificação da tarefa: operação recusada; tarefa de A inalterada.
- A conta Google está indisponível ou o Google não confirma a identidade por erro: a pessoa permanece desconectada e vê mensagem clara; a lista não abre.
- Primeira visita após autenticar, sem tarefas próprias: lista vazia, sem herdar tarefas de ninguém.
- Sessão encerrada e novo pedido ao sistema: tratado como sem sessão.
- Segredo do aplicativo Google em mensagem de erro, tela ou repositório: não pode aparecer em nenhum desses lugares.
- Tarefas sem dono após a feature entrar em vigor: permanecem inacessíveis a pessoas autenticadas até decisão humana explícita (não há atribuição automática).

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: O sistema MUST mostrar, a quem não autenticou, apenas a tela de entrada com a ação de continuar com Google — sem lista de tarefas e sem formulário de nova tarefa.
- **FR-002**: O sistema MUST permitir que a pessoa continue com a conta Google e, quando o Google confirmar a identidade, MUST devolvê-la ao aplicativo já identificada.
- **FR-003**: Depois de autenticada, a pessoa MUST ver somente as tarefas das quais ela é dona. Se não houver nenhuma, a lista MUST aparecer vazia.
- **FR-004**: Criar, concluir, arquivar e excluir tarefa MUST valer apenas para tarefas de quem está autenticado.
- **FR-005**: O sistema MUST recusar qualquer tentativa de concluir, arquivar ou excluir tarefa de outra pessoa.
- **FR-006**: Qualquer pedido ao sistema para listar, criar, concluir, arquivar ou excluir tarefa, feito sem sessão válida, MUST ser recusado e MUST NOT devolver tarefa de ninguém.
- **FR-007**: A pessoa autenticada MUST poder encerrar a sessão.
- **FR-008**: Depois de encerrar a sessão, o sistema MUST voltar a pessoa à tela de entrada e MUST tratar os pedidos seguintes como sem sessão até nova autenticação.
- **FR-009**: Se a pessoa cancelar ou recusar o consentimento no Google, o sistema MUST mantê-la desconectada, MUST mostrar mensagem clara de que a entrada não foi concluída, e MUST NOT abrir a lista.
- **FR-010**: Tarefas que já existiam sem dono MUST NOT aparecer para qualquer pessoa autenticada.
- **FR-011**: Tarefas sem dono MUST NOT ser atribuídas automaticamente à primeira pessoa que autenticar (nem a qualquer pessoa seguinte).
- **FR-012**: Segredos do aplicativo Google MUST NOT aparecer na tela, no repositório do projeto nem em mensagem de erro.
- **FR-013**: A mesma conta Google MUST ser reconhecida como a mesma pessoa em visitas posteriores, para que ela reencontre as próprias tarefas.
- **FR-014**: Enquanto autenticada, a pessoa MUST conseguir identificar quem está na sessão (por nome ou e-mail fornecidos pelo Google).
- **FR-015**: Se o Google não confirmar a identidade por erro ou indisponibilidade, o sistema MUST manter a pessoa desconectada e MUST mostrar mensagem clara, sem abrir a lista e sem expor segredos.

### Key Entities

- **Pessoa autenticada**: Quem concluiu a entrada com Google. Tem uma sessão válida e é dona apenas das tarefas que ela cria depois de autenticada.
- **Conta Google**: Identidade externa usada para entrar. A mesma conta corresponde à mesma pessoa neste aplicativo. Não se une a outra identidade nesta feature.
- **Sessão**: Período em que a pessoa permanece identificada no aplicativo. Sem sessão válida, não há acesso à lista nem a operações sobre tarefas. Encerra-se quando a pessoa sai ou quando a sessão deixa de ser válida.
- **Tarefa**: Item da lista (título, concluída ou não, arquivada ou não) que pertence a exatamente uma pessoa autenticada depois desta feature.
- **Tarefa sem dono**: Tarefa criada antes desta feature, sem pessoa associada. Não aparece para quem autenticou e não é herdada. Permanece inacessível até decisão humana explícita sobre o destino desses dados.

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: Uma pessoa que já possui conta Google conclui a entrada e vê a lista dela (ou a lista vazia) em menos de 2 minutos, contados a partir da tela de entrada.
- **SC-002**: Em 100% das visitas sem sessão válida, a lista de tarefas e o formulário de nova tarefa não são exibidos.
- **SC-003**: Depois de autenticar, 100% das tarefas visíveis na lista pertencem à pessoa da sessão.
- **SC-004**: Em verificações de isolamento com duas contas Google distintas, a pessoa B não vê nem altera tarefas da pessoa A em 100% das tentativas.
- **SC-005**: Depois de encerrar a sessão, a pessoa volta à tela de entrada e deixa de ver a lista; um novo pedido sem autenticar é recusado.
- **SC-006**: Em 100% dos cancelamentos ou recusas de consentimento no Google, a pessoa permanece desconectada, vê uma mensagem compreensível (sem jargão técnico) e a lista não abre.
- **SC-007**: Em 100% dos casos, tarefas antigas sem dono não aparecem para pessoa autenticada alguma e não são atribuídas a quem entra primeiro.
- **SC-008**: Uma revisão da tela, das mensagens de erro e do repositório do projeto encontra zero segredos do aplicativo Google.

## Assumptions

- Nesta feature o único provedor de identidade é o Google. Entrar com GitHub fica para uma feature seguinte.
- Não há cadastro com e-mail e senha, recuperação de senha, segundo fator nem papéis de administrador.
- Não se une a conta Google a outra identidade (são identidades separadas).
- A lista anônima global deixa de ser o modo de uso: ninguém permanece nela depois desta feature.
- A pessoa precisa de conta Google e de conexão com a internet para autenticar.
- A mesma conta Google é sempre a mesma pessoa neste aplicativo. Compartilhar uma conta Google entre várias pessoas está fora de escopo.
- Lista vazia na primeira autenticação é o comportamento correto, não um erro.
- Tarefas sem dono permanecem armazenadas, porém inacessíveis a quem autenticou. Não são apagadas automaticamente nesta feature.
- Antes de passar a exigir dono nas tarefas já existentes, a execução para para checkpoint humano: a pessoa revisa o impacto nas tarefas sem dono e decide aprovar, editar ou voltar a esta spec. Não há atribuição automática.
- A sessão permanece válida até a pessoa encerrar ou até a sessão deixar de ser válida segundo a prática usual de aplicativos na web (expiração por inatividade prolongada). A pessoa não precisa reentrar a cada clique.
- Na tela autenticada, mostra-se nome ou e-mail obtidos do Google, o suficiente para a pessoa saber quem está na sessão.
- Mensagens de falha (cancelamento, recusa, indisponibilidade do Google) são em linguagem clara, sem códigos internos e sem segredos.
- Tornar pública a lista de outra pessoa está fora de escopo.

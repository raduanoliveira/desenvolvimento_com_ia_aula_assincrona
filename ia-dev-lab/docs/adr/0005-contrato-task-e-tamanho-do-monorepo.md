# 0005-contrato-task-e-tamanho-do-monorepo.md (formato MADR)

## Contexto

Na revisão da Etapa 4, o ToDo já tinha dois processos no Docker (tela React e API Laravel), camadas POSA e um porto de identidade. O ponto fraco era o contrato da tarefa: `TaskRepositoryInterface` devolvia o Model Eloquent, e os Services lançavam `ModelNotFoundException`. Ao mesmo tempo, cabia perguntar se tarefa ou login deveriam virar outro processo. Extrair serviço aumentaria acoplamento de rede, cookie e deploy, sem segundo consumidor. Fechar só o tipo da aplicação mexia em Services e testes, sem migration e sem mudar a tela.

## Decisão

Mantive o monorepo no tamanho certo: não extraí API de tarefas nem de login. Fechei o contrato da aplicação com `App\Domain\Task`. O repositório Eloquent traduz persistência; o Service lança `TaskNotFoundException`. Os ADRs 0001 a 0004 permaneceram intactos.

## Consequencias

Ganho: a regra de negócio deixa de depender do ORM no tipo; o teste de unidade usa entidade, não Model; o diagrama C4 não precisa promover Eloquent a contêiner. Perda: mapeamento extra no Adapter e mais um tipo para a IA respeitar. Não ganhei OpenAPI nem um segundo cliente. Se a API for consumida por outro sistema, o contrato HTTP ainda é implícito.

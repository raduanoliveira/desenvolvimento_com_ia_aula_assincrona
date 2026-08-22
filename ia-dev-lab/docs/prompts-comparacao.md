# Comparação de prompts: arquivar tarefa

Funcionalidade: arquivar uma tarefa da lista, sem apagar o registro.

Eu rodei o mesmo pedido de duas formas (prompt fraco e prompt eficaz) em dois modelos. Não inventei o resultado: cada abordagem gerou código de verdade, em pastas separadas. O código que ficou no laboratório é o do prompt eficaz no modelo principal.

## Modelos

- Modelo principal: Cursor Grok 4.6
- Modelo de menor performance: Composer 2.5 Fast

## Prompt fraco

Adicione arquivar tarefa.

## Prompt eficaz

Contexto: este é um ToDo em monorepo. A API é Laravel. A tela é React com TypeScript, Context API e MUI. Arquivar tarefa é um caso de uso novo: a tarefa deixa a lista ativa sem ser apagada. O código já tem criar, listar, concluir e excluir.

Exemplo de padrão a seguir: teste primeiro. Um caso de uso por Service. Controller só HTTP. Persistência por interface de repositório. Na tela, o componente não chama a API direto.

Restrições: não colocar regra de negócio no controller nem na tela. Não pular camada. Não inchar um Service que já existe. Não pular testes.

Validação: teste da rota de arquivar e teste de unidade do Service. Na tela, ação de arquivar com teste de componente.

## O que cada abordagem fez

### Grok 4.6, prompt fraco

A IA criou a rota de arquivar e um botão na lista. A regra ficou no controller: busca a tarefa, marca como arquivada e salva ali mesmo. Não criou Service novo. Não escreveu teste de arquivar. A listagem da API continua devolvendo tudo, inclusive o que foi arquivado. Na tela, o item arquivado permanece na lista, só muda o rótulo.

### Grok 4.6, prompt eficaz

A IA criou um Service só para arquivar. O controller só chama esse Service. A lista ativa filtra o que está arquivado. Escreveu teste de unidade do Service, teste da rota e testes na tela. Eu conferi a execução: nove testes da API passaram e sete da tela passaram.

### Composer 2.5 Fast, prompt fraco

Mesmo pedido curto. Desta vez a IA criou um Service de arquivar e o controller só delegou. Ainda assim não escreveu nenhum teste novo. A listagem escondeu arquivadas direto no repositório.

### Composer 2.5 Fast, prompt eficaz

A IA também criou o Service, a rota e o botão. Escreveu teste de unidade, teste da rota e teste do botão na lista. Rodou os testes no Docker: oito da API passaram e seis da tela passaram. Não chegou no mesmo conjunto de testes do Grok com o prompt eficaz (faltou o teste de unidade da lista ativa e os testes do gateway e do Context).

## Diferenças que eu observei

No prompt fraco, o pedido curto não disse onde a regra deveria viver nem como validar. No Grok isso caiu no controller, sem teste e sem tirar a tarefa da lista ativa. No Composer o código copiou o jeito dos outros Services, mas mesmo assim pulou os testes.

No prompt eficaz, os dois modelos abriram um caso de uso novo, mantiveram o controller magro e escreveram testes. A diferença de qualidade apareceu mais no Grok: ele cobriu unidade, rota e tela e eu vi os testes passarem no laboratório. O Composer atendeu o pedido, com menos testes.

O modelo mais fraco, com o prompt curto, não desandou tanto quanto o Grok no controller, porque o projeto já tinha Services para copiar. Ainda assim o prompt eficaz foi o que fez os dois modelos testarem de verdade.

# 0004. Camada Service no Laravel

## Status

Aceita.

## Contexto

No Laravel, muita gente coloca regra de negócio no controller. Isso mistura HTTP com a regra da aplicação e dificulta teste e troca de persistência.

## Decisão

Eu incluí uma camada Service, que não vem por padrão no Laravel. O controller só trata HTTP. A regra da tarefa fica no Service. Os dados passam por uma abstração de repositório.

## Por quê

Eu queria duas coisas ao mesmo tempo: o controller só falando de HTTP, e um exercício de engenharia de software (SOLID, camadas POSA e um caso de uso por classe).

## Consequências

Caso de uso novo vira um Service novo. Teste de unidade cobre a regra sem HTTP e sem banco. Teste de feature cobre a rota. A IA não deve gravar regra de negócio no controller.

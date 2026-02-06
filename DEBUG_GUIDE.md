# 🔧 Guia de Debug - Editar/Deletar Funcionários

## Problema

Ao tentar editar ou deletar funcionários, o sistema retorna um erro.

## Solução Implementada

- ✅ Reescrito `EmployeeEdit.php` usando `exit(json_encode(...))`
- ✅ Reescrito `EmployeeDelete.php` usando `exit(json_encode(...))`
- ✅ Melhorado JavaScript em `EmployeeHere.php` para capturar erros completos
- ✅ Adicionado Header de Content-Type

## Como Testar

### 1️⃣ Primeiro: Verifique se está logado como Empresa

- Acesse `/dashboard-manager/index.html`
- Faça login como uma empresa (ex: "Teste1")
- Navegue até "Funcionários"

### 2️⃣ Abra o Console do Navegador (F12)

- Abra as abas "Console" e "Network"
- Tente **editar** um funcionário
- **Verifique os logs:**
  - Console: qual mensagem aparece?
  - Network → clique em "EmployeeEdit.php": qual é o status da resposta?

### 3️⃣ Teste com nosso arquivo de diagnóstico

```
http://localhost/dashboard-manager/test-ajax.php
```

- Selecione uma empresa
- Selecione um funcionário
- Clique em "Testar EDIT" ou "Testar DELETE"
- Veja a resposta completa

### 4️⃣ Verifique os Logs

```
http://localhost/dashboard-manager/view-logs.php
```

- Veja se há erro SQL ou de conexão

## Possíveis Erros e Soluções

| Erro                         | Causa                         | Solução                                            |
| ---------------------------- | ----------------------------- | -------------------------------------------------- |
| "Não autenticado"            | Sessão perdida                | Faça login novamente                               |
| "Empresa não encontrada"     | usuario_id inválido           | Verifique se o `usuario_id` está correto na sessão |
| "Funcionário não encontrado" | Funcionário não e da empresa  | Dele é da mesma empresa?                           |
| "Email já cadastrado"        | Outro funcionário tem o email | Use um email diferente                             |
| HTML em vez de JSON          | Erro fatal do PHP             | Veja os logs do servidor                           |

## Arquivos Modificados

- `/controllers/EmployeeEdit.php` - Agora usa `exit(json_encode(...))`
- `/controllers/EmployeeDelete.php` - Agora usa `exit(json_encode(...))`
- `/frontend/pages/Enterprise/EmployeeHere.php` - Melhor tratamento de erros no JavaScript

## Próximos Passos

Se ainda houver erro:

1. Abra `test-ajax.php` e rode um teste
2. Cole a resposta completa nos comentários
3. Verificarei o erro específico

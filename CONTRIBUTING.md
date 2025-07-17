# Contributing to Tecnospeed Serasa Package

Obrigado por considerar contribuir para o pacote Tecnospeed Serasa! 

## Como Contribuir

### Reportando Bugs

1. **Verifique se o bug já foi reportado** - Procure nos [issues existentes](https://github.com/Ferreiramg/serasa/issues)
2. **Crie um novo issue** - Se não encontrar nada similar
3. **Seja específico** - Inclua:
   - Versão do PHP
   - Versão do Laravel
   - Passos para reproduzir o bug
   - Comportamento esperado vs atual
   - Screenshots se aplicável

### Sugerindo Melhorias

1. **Abra um issue** descrevendo a melhoria
2. **Explique o caso de uso**
3. **Forneça exemplos** se possível

### Pull Requests

1. **Fork o repositório**
2. **Crie uma branch** para sua feature: `git checkout -b feature/nova-funcionalidade`
3. **Faça suas alterações**
4. **Adicione testes** se aplicável
5. **Execute os testes**: `composer test`
6. **Execute o linter**: `composer format`
7. **Commit suas alterações**: `git commit -am 'Adiciona nova funcionalidade'`
8. **Push para a branch**: `git push origin feature/nova-funcionalidade`
9. **Abra um Pull Request**

## Diretrizes de Código

- Siga os padrões PSR-12
- Use type hints sempre que possível
- Adicione docblocks aos métodos públicos
- Mantenha os métodos pequenos e focados
- Escreva testes para novas funcionalidades

## Configuração do Ambiente de Desenvolvimento

```bash
# Clone o repositório
git clone https://github.com/Ferreiramg/serasa.git
cd serasa

# Instale as dependências
composer install

# Execute os testes
composer test

# Execute o linter
composer format
```

## Padrões de Commit

Use mensagens de commit claras e descritivas:

- `feat: adiciona nova funcionalidade`
- `fix: corrige bug específico`
- `docs: atualiza documentação`
- `test: adiciona ou corrige testes`
- `refactor: refatora código sem mudanças funcionais`

## Dúvidas?

Se tiver dúvidas, abra um issue ou entre em contato!

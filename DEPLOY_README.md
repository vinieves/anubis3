# 🚀 Scripts de Deploy - Sistema Anubis

## 📋 Scripts Disponíveis

### `deploy.sh` - Deploy Completo
Atualiza o código, limpa cache e reinicia serviços.

```bash
chmod +x deploy.sh
./deploy.sh
```

### `check-env.sh` - Diagnóstico
Verifica se as variáveis de ambiente estão configuradas corretamente.

```bash
chmod +x check-env.sh
./check-env.sh
```

---

## ⚠️ IMPORTANTE - Resolver erro de CONNECTION_URL

Se aparecer o erro:
```
Error: Exactly one of browserWSEndpoint, browserURL or transport must be passed to puppeteer.connect
```

### Causa:
A variável `CONNECTION_URL` não está sendo lida pelo Laravel.

### Solução:

1. **Edite o .env e REMOVA as aspas:**

```bash
nano .env
```

**ERRADO:**
```env
CONNECTION_URL="wss://browser.zenrows.com?apikey=xxx"
```

**CORRETO:**
```env
CONNECTION_URL=wss://browser.zenrows.com?apikey=xxx
```

2. **Limpe o cache:**

```bash
php artisan config:clear
php artisan cache:clear
php artisan config:cache
```

3. **Verifique se funcionou:**

```bash
php artisan tinker --execute="echo env('CONNECTION_URL');"
```

Deve retornar a URL completa, NÃO null.

---

## 🔄 Fluxo de Atualização

### Na sua máquina local (Cursor):

```bash
git add .
git commit -m "Descrição da alteração"
git push
```

### Na VPS:

```bash
cd /var/www/anubis
./deploy.sh
```

Pronto! O script faz tudo automaticamente.

---

## 🐛 Diagnóstico de Problemas

Execute o script de diagnóstico:

```bash
./check-env.sh
```

Ele vai verificar:
- ✅ Se CONNECTION_URL está no .env
- ✅ Se o Laravel consegue ler
- ✅ Se há cache antigo
- ✅ Se o Node.js funciona

---

## 📝 Ordem Correta dos Comandos

Sempre nesta ordem:

1. `git pull` - Baixa código
2. `php artisan optimize:clear` - Limpa TUDO
3. `php artisan config:clear` - Limpa config
4. `php artisan cache:clear` - Limpa cache
5. `php artisan config:cache` - Recria cache (com .env atualizado)
6. `php artisan optimize` - Otimiza
7. Reinicia serviços

❌ **NUNCA** faça `config:cache` ANTES de limpar o cache antigo!

---

## 💡 Dicas

- Sempre use `./deploy.sh` após fazer `git push`
- Se der erro, execute `./check-env.sh` para diagnosticar
- Mantenha o `.env` SEM aspas nas URLs
- Teste sempre com `php artisan tinker --execute="echo env('CONNECTION_URL');"`

---

## 🆘 Ajuda Rápida

**Erro de permissão:**
```bash
chmod +x deploy.sh check-env.sh
```

**Cache travado:**
```bash
php artisan optimize:clear
rm -rf bootstrap/cache/*
php artisan config:cache
```

**Testar bot manualmente:**
```bash
node scripts/bot.js "185737081:1" "Test" "User" "test@test.com" "123" "1" "4111111111111111" "12" "25" "123" "wss://sua-url-aqui"
```


# 🚀 Sistema /pay - Guia de Configuração Completo

## ✅ **O QUE FOI IMPLEMENTADO**

### **Arquivos Criados:**

```
Backend (PHP):
✅ config/pixels.php - Configuração de pixels
✅ app/Services/FacebookPixelService.php - Serviço de tracking
✅ app/Http/Controllers/Pay/PayController.php - Controller principal
✅ app/Http/Controllers/Pay/PayUpsellController.php - Upsell 1
✅ app/Http/Controllers/Pay/PayUpsell2Controller.php - Upsell 2
✅ routes/web.php - Rotas do /pay adicionadas

Frontend (Views):
✅ resources/views/pay/index.blade.php - Checkout principal
✅ resources/views/pay/upsell1.blade.php - Primeira oferta upsell
✅ resources/views/pay/upsell2.blade.php - Segunda oferta upsell
✅ resources/views/pay/thankyou.blade.php - Página de obrigado
```

---

## 📋 **PASSO 1: Configurar o .env**

Adicione estas linhas no seu arquivo `.env`:

```env
# ============================================
# FACEBOOK PIXEL - CHECKOUT ANTIGO (Backup)
# ============================================
PIXEL_CHECKOUT=
PIXEL_CHECKOUT_TOKEN=
PIXEL_CHECKOUT_ENABLED=false

# ============================================
# FACEBOOK PIXEL - NOVO SISTEMA /PAY
# ============================================
PIXEL_PAY=SEU_PIXEL_ID_AQUI
PIXEL_PAY_TOKEN=SEU_TOKEN_AQUI
PIXEL_PAY_ENABLED=true

# ============================================
# MODO DE TESTE (Opcional)
# ============================================
PIXEL_TEST_MODE=false
PIXEL_TEST_EVENT_CODE=
```

### **Como obter o Pixel ID e Token:**

1. **Pixel ID:**
   - Acesse: https://business.facebook.com/events_manager
   - Clique no seu Pixel
   - Copie o ID (número de 15 dígitos)

2. **Conversion API Token:**
   - No Events Manager, vá em: **Settings → Generate Access Token**
   - Copie o token gerado
   - Cole em `PIXEL_PAY_TOKEN`

---

## 🚀 **PASSO 2: Testar o Sistema**

### **URLs Disponíveis:**

```
✅ Checkout Principal:
https://seusite.com/pay/

✅ Com Oferta Específica:
https://seusite.com/pay/?id=oferta1
https://seusite.com/pay/?id=oferta2
https://seusite.com/pay/?id=oferta3
https://seusite.com/pay/?id=oferta4

✅ Upsells:
https://seusite.com/pay/upsell1
https://seusite.com/pay/upsell2

✅ Thank You:
https://seusite.com/pay/thankyou
```

---

## 🎯 **PASSO 3: Testar Facebook Pixel**

### **Instalar Facebook Pixel Helper:**
1. Acesse: https://chrome.google.com/webstore
2. Busque: "Facebook Pixel Helper"
3. Instale a extensão

### **Verificar Eventos:**
1. Abra: `https://seusite.com/pay/`
2. Clique no ícone do Pixel Helper
3. Deve mostrar:
   - ✅ **PageView**
   - ✅ **ViewContent**

4. Preencha o nome → Deve disparar:
   - ✅ **InitiateCheckout**

5. Digite o número do cartão → Deve disparar:
   - ✅ **AddPaymentInfo**

6. Complete a compra → Deve disparar:
   - ✅ **Purchase** (na página thankyou)

---

## 📊 **EVENTOS DO FACEBOOK PIXEL**

| Evento | Quando Dispara | Dados Enviados |
|--------|---------------|----------------|
| **PageView** | Carrega /pay | URL, oferta |
| **ViewContent** | Carrega /pay | Produto, preço, ID |
| **InitiateCheckout** | Foca no campo nome | Valor, moeda |
| **AddPaymentInfo** | Digita cartão | Valor, moeda |
| **Purchase** 🎯 | Venda aprovada | Valor, ID transação |
| **PaymentDeclined** | Venda recusada | Motivo, valor |

---

## 🔥 **DIFERENÇAS ENTRE /checkout e /pay**

| Aspecto | `/checkout` (Antigo) | `/pay` (Novo) |
|---------|---------------------|---------------|
| **Status** | ✅ Backup ativo | ✅ Sistema principal |
| **Pixel** | Opcional/Desabilitado | Facebook Pixel completo |
| **Design** | Design atual | Design moderno limpo |
| **Tracking** | Básico | Avançado (todos eventos) |
| **Sessões** | `session('customer_*')` | `session('pay_customer_*')` |
| **Rotas** | `/checkout`, `/upsell1` | `/pay`, `/pay/upsell1` |
| **Logs** | `[Checkout]` | `[PAY]` |

---

## 🛠️ **TROUBLESHOOTING**

### **Problema: Pixel não dispara eventos**

**Solução:**
1. Verifique se `PIXEL_PAY_ENABLED=true` no `.env`
2. Rode: `php artisan config:clear`
3. Verifique se o Pixel ID está correto
4. Instale o Facebook Pixel Helper

### **Problema: Erro 404 ao acessar /pay**

**Solução:**
1. Rode: `php artisan route:clear`
2. Verifique se as rotas foram adicionadas em `routes/web.php`

### **Problema: Venda não processa**

**Solução:**
1. Verifique os logs: `storage/logs/laravel.log`
2. Procure por: `[PAY]` nos logs
3. Verifique se o `CartPandaService` está funcionando

---

## 🎯 **MONITORAMENTO**

### **Facebook Events Manager:**
1. Acesse: https://business.facebook.com/events_manager
2. Clique no seu Pixel
3. Vá em: **Test Events**
4. Faça uma compra de teste
5. Veja os eventos em tempo real

### **Logs do Sistema:**
```bash
# Ver logs em tempo real
tail -f storage/logs/laravel.log | grep "\[PAY\]"
```

---

## 📈 **MÉTRICAS IMPORTANTES**

No Facebook Events Manager, monitore:

- **ViewContent:** Quantas pessoas viram a oferta
- **InitiateCheckout:** Quantas começaram o checkout
- **AddPaymentInfo:** Quantas adicionaram dados de pagamento
- **Purchase:** 🎯 **CONVERSÕES** (o mais importante!)
- **Taxa de Conversão:** Purchase / ViewContent

---

## 🚀 **PRÓXIMOS PASSOS (Opcional)**

### **1. Conversion API Server-Side:**
Já está implementado no `FacebookPixelService`! Os eventos são enviados tanto client-side (navegador) quanto server-side (PHP) para maior precisão.

### **2. Criar Campanhas no Facebook:**
- Use o Pixel do `/pay` nas suas campanhas
- Otimize para o evento **Purchase**
- Facebook vai aprender com as conversões

### **3. A/B Testing:**
Teste qual checkout converte mais:
- 50% do tráfego → `/checkout`
- 50% do tráfego → `/pay`
- Compare as conversões

---

## ✅ **CHECKLIST FINAL**

Antes de usar em produção:

- [ ] `.env` configurado com Pixel ID e Token
- [ ] `php artisan config:clear` executado
- [ ] Facebook Pixel Helper instalado
- [ ] Teste completo realizado (PageView → Purchase)
- [ ] Events Manager mostrando eventos
- [ ] Logs verificados sem erros
- [ ] Backup do /checkout funcionando

---

## 🎉 **CONCLUSÃO**

Você agora tem **2 checkouts funcionando**:

1. **`/checkout`** - Backup seguro (sistema antigo)
2. **`/pay`** - Novo sistema com Facebook Pixel completo

**Vantagens:**
✅ Tracking completo do Facebook
✅ Otimização de campanhas
✅ Backup seguro funcionando
✅ Testes A/B possíveis
✅ Métricas detalhadas

**Está tudo pronto para uso! 🚀**

---

## 📞 **SUPORTE**

Se tiver dúvidas:
1. Verifique os logs: `storage/logs/laravel.log`
2. Use o Facebook Pixel Helper
3. Teste com o Events Manager → Test Events

**Boa sorte com suas vendas! 💰**


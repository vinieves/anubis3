#!/bin/bash

# Script de Diagnóstico - Verificar variáveis de ambiente
# Autor: Sistema Anubis

echo "=========================================="
echo "🔍 Diagnóstico de Variáveis de Ambiente"
echo "=========================================="
echo ""

cd /var/www/anubis || exit

echo "1️⃣ Verificando .env:"
echo "---"
if grep -q "CONNECTION_URL" .env; then
    echo "✅ CONNECTION_URL encontrada no .env"
    grep "CONNECTION_URL" .env | head -1
else
    echo "❌ CONNECTION_URL NÃO encontrada no .env"
fi

echo ""
echo "2️⃣ Verificando leitura pelo Laravel:"
echo "---"
php artisan tinker --execute="
\$url = env('CONNECTION_URL');
if (\$url) {
    echo '✅ Laravel consegue ler: ' . substr(\$url, 0, 50) . '...' . PHP_EOL;
} else {
    echo '❌ Laravel retorna NULL' . PHP_EOL;
}
"

echo ""
echo "3️⃣ Verificando cache de configuração:"
echo "---"
if [ -f bootstrap/cache/config.php ]; then
    echo "⚠️  Cache de configuração existe"
    echo "   Execute: php artisan config:clear"
else
    echo "✅ Sem cache de configuração"
fi

echo ""
echo "4️⃣ Teste direto do Node.js:"
echo "---"
node scripts/bot.js "185737081:1" "Test" "User" "test@test.com" "123456" "1" "4111111111111111" "12" "25" "123" "wss://test.com" 2>&1 | head -5

echo ""
echo "=========================================="
echo "📋 Recomendações:"
echo "=========================================="
echo "Se CONNECTION_URL retorna NULL no Laravel:"
echo "  1. Remova aspas da variável no .env"
echo "  2. Execute: php artisan config:clear"
echo "  3. Execute: php artisan config:cache"
echo ""


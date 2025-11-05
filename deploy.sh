#!/bin/bash

# Script de Deploy e Atualização - Anubis
# Autor: Sistema Anubis
# Data: 2025-11-05

echo "=========================================="
echo "🚀 Iniciando atualização do sistema..."
echo "=========================================="
echo ""

# Navega para o diretório do projeto
cd /var/www/anubis || exit

echo "📥 Puxando atualizações do Git..."
git pull origin main

if [ $? -ne 0 ]; then
    echo "⚠️  Erro ao fazer git pull. Tentando com master..."
    git pull origin master
fi

echo ""
echo "🧹 Limpando caches do Laravel..."
php artisan optimize:clear
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

echo ""
echo "⚙️  Recarregando variáveis de ambiente..."
php artisan config:cache

echo ""
echo "🔧 Otimizando aplicação..."
php artisan optimize

echo ""
echo "🔄 Reiniciando serviços..."

# Detecta qual servidor web está rodando e reinicia
if systemctl is-active --quiet php8.2-fpm; then
    echo "Reiniciando PHP-FPM..."
    systemctl restart php8.2-fpm
elif systemctl is-active --quiet php8.1-fpm; then
    echo "Reiniciando PHP-FPM..."
    systemctl restart php8.1-fpm
elif systemctl is-active --quiet php-fpm; then
    echo "Reiniciando PHP-FPM..."
    systemctl restart php-fpm
fi

if systemctl is-active --quiet nginx; then
    echo "Reiniciando Nginx..."
    systemctl restart nginx
elif systemctl is-active --quiet apache2; then
    echo "Reiniciando Apache..."
    systemctl restart apache2
fi

echo ""
echo "=========================================="
echo "✅ Atualização concluída com sucesso!"
echo "=========================================="
echo ""
echo "🔍 Verificando configuração importante..."
php artisan tinker --execute="echo 'CONNECTION_URL: ' . (env('CONNECTION_URL') ? 'CONFIGURADA ✅' : 'NÃO ENCONTRADA ❌');"

echo ""
echo "📊 Status dos serviços:"
systemctl status php8.2-fpm --no-pager -l | head -3
systemctl status nginx --no-pager -l | head -3

echo ""
echo "🎉 Sistema atualizado e pronto para uso!"


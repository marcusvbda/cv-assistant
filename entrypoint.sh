#!/bin/sh
set -e

echo "🔧 Corrigindo permissões..."
mkdir -p storage/framework/{sessions,views,cache} bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

if [ ! -d "vendor" ]; then
  echo "📦 Instalando dependências PHP com Composer..."
  composer install
fi

echo "⏳ Aguardando banco de dados ficar disponível..."
until nc -z -v -w30 mysql 3306
do
  echo "Banco não disponível, esperando 5 segundos..."
  sleep 5
done

echo "🚀 Iniciando PHP-FPM..."
exec php-fpm

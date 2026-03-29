#!/bin/bash
set -e

echo "========================================="
echo "  SweetManager - Iniciando ambiente..."
echo "========================================="

# Copia o .env se não existir
if [ ! -f .env ]; then
    echo "[1/5] Criando arquivo .env..."
    cp .env.example .env
fi

# Força as variáveis de banco para apontar ao container MySQL
sed -i "s/DB_HOST=.*/DB_HOST=mysql/" .env
sed -i "s/DB_PORT=.*/DB_PORT=3306/" .env
sed -i "s/DB_DATABASE=.*/DB_DATABASE=laravel/" .env
sed -i "s/DB_USERNAME=.*/DB_USERNAME=root/" .env
sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=root/" .env

# Instala dependências PHP
if [ ! -d "vendor" ]; then
    echo "[2/5] Instalando dependências do Composer..."
    composer install --no-interaction --optimize-autoloader
else
    echo "[2/5] Dependências já instaladas."
fi

# Gera a chave da aplicação
echo "[3/5] Gerando chave da aplicação..."
php artisan key:generate --force

# Aguarda o MySQL com teste TCP puro (sem depender de cliente mysql)
echo "[4/5] Aguardando MySQL ficar disponível..."
MAX_TRIES=40
COUNT=0
until php -r "
    \$conn = @fsockopen('mysql', 3306, \$errno, \$errstr, 2);
    if (\$conn) { fclose(\$conn); exit(0); }
    exit(1);
" 2>/dev/null; do
    COUNT=$((COUNT + 1))
    if [ $COUNT -ge $MAX_TRIES ]; then
        echo "ERRO: MySQL não respondeu após 120 segundos."
        exit 1
    fi
    echo "     Tentativa $COUNT/$MAX_TRIES — aguardando 3s..."
    sleep 3
done

# Espera extra para o MySQL terminar de inicializar após aceitar conexões TCP
echo "     Porta aberta! Aguardando MySQL inicializar completamente..."
sleep 5
echo "     MySQL disponível!"

# Roda as migrations
echo "[5/5] Rodando migrations..."
php artisan migrate --force

echo "========================================="
echo "  Tudo pronto! Acessar em:"
echo "  http://localhost:8000"
echo "  phpMyAdmin: http://localhost:8080"
echo "========================================="

# Inicia o servidor
exec php artisan serve --host=0.0.0.0 --port=8000

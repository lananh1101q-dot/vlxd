#!/bin/bash
echo "Khoi dong He thong Microservices Quan Ly Kho VLXD..."

# Khai bao duong dan PHP
PHP_CMD="php"
if ! command -v php &> /dev/null; then
    if [ -f "/c/xampp/php/php.exe" ]; then
        PHP_CMD="/c/xampp/php/php.exe"
    elif [ -f "/d/xampp/php/php.exe" ]; then
        PHP_CMD="/d/xampp/php/php.exe"
    else
        echo "Loi: Khong tim thay lenh 'php' (khong co trong PATH va khong thay thu muc xampp)."
        exit 1
    fi
fi

echo "Su dung PHP tai: $PHP_CMD"
echo ""

echo "Khoi dong API Gateway (Port 8000)..."
$PHP_CMD -S 127.0.0.1:8000 -t api-gateway &

echo "Khoi dong User Service (Port 3001)..."
$PHP_CMD -S 127.0.0.1:3001 -t services/user-service &

echo "Khoi dong Product Service (Port 3002)..."
$PHP_CMD -S 127.0.0.1:3002 -t services/product-service &

echo "Khoi dong Warehouse Service (Port 3003)..."
$PHP_CMD -S 127.0.0.1:3003 -t services/warehouse-service &

echo "Khoi dong Manufacturing Service (Port 3004)..."
$PHP_CMD -S 127.0.0.1:3004 -t services/manufacturing-service &

echo "Khoi dong Customer Service (Port 3005)..."
$PHP_CMD -S 127.0.0.1:3005 -t services/customer-service &

echo "----------------------------------------------------"
echo "Da bat tong cong 6 server duoi nen."
echo "API Gateway dang chay tai: http://127.0.0.1:8000"
echo "GUI Client dang chay tai:  http://localhost/vlxd/dangnhap.php"
echo "An Enter de dung cac server nay (hoac chay lenh killall php)..."
echo "----------------------------------------------------"
read -p ""
kill $(jobs -p)
echo "Da dung tat ca services."

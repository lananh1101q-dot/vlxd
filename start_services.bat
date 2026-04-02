@echo off
echo Khoi dong He thong Microservices Quan Ly Kho VLXD...

echo Dang khoi dong cac PHP Built-in Servers...
start "API Gateway (Port 8000)" cmd /k "php -S 127.0.0.1:8000 -t api-gateway"
start "User Service (Port 3001)" cmd /k "php -S 127.0.0.1:3001 -t services/user-service"
start "Product Service (Port 3002)" cmd /k "php -S 127.0.0.1:3002 -t services/product-service"
start "Warehouse Service (Port 3003)" cmd /k "php -S 127.0.0.1:3003 -t services/warehouse-service"
start "Customer Service (Port 3005)" cmd /k "php -S 127.0.0.1:3005 -t services/customer-service"
start "Manufacturing Service (Port 3004)" cmd /k "php -S 127.0.0.1:3004 -t services/manufacturing-service"

echo ----------------------------------------------------
echo Da bat tong cong 6 server (Gateway + 5 Services).
echo API Gateway dang chay tai: http://127.0.0.1:8000
echo GUI Client dang chay tai:  http://localhost/vlxd/dangnhap.php
echo ----------------------------------------------------
pause

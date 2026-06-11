ssh -i "C:\Users\LENOVO\.ssh\infomaniak4_fixed.key" -o StrictHostKeyChecking=no ubuntu@83.228.206.120 "find /var/www/ -name .env 2>/dev/null || find /home/ubuntu/ -name .env 2>/dev/null"

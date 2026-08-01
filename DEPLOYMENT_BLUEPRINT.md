# Deployment & Infrastructure Blueprint - Website Desa Kalimati

## 1. Server Environment & Prerequisites

### Target VPS Specs (Recommended)
- OS: Ubuntu 22.04 LTS / 24.04 LTS
- CPU: Minimal 2 vCPU
- RAM: Minimal 2 GB (4 GB recommended untuk build Vite/Tailwind)
- Storage: 20 GB SSD
- Web Server: Nginx
- PHP Runtime: PHP 8.2-FPM atau PHP 8.3-FPM
- Database: MySQL 8.0+ / PostgreSQL 15+

---

## 2. Nginx Web Server Configuration

Simpan file ini di /etc/nginx/sites-available/desa-kalimati lalu buat symlink ke /etc/nginx/sites-enabled/.

FILE: /etc/nginx/sites-available/desa-kalimati
==================================================
server {
    listen 80;
    listen [::]:80;
    server_name kalimati.desa.id www.kalimati.desa.id;
    root /var/www/desa-kalimati/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header X-XSS-Protection "1; mode=block";

    index index.php;
    charset utf-8;

    # Max upload size (Penting untuk upload dokumen Research Hub & Media)
    client_max_body_size 32M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
==================================================

---

## 3. Systemd Service Configuration (Queue & Scheduler)

### A. Laravel Queue Worker (/etc/systemd/system/kalimati-worker.service)
Digunakan untuk memproses antrean telemetry IoT, pemanggilan LLM/RAG, dan pengolahan gambar di background.

FILE: /etc/systemd/system/kalimati-worker.service
==================================================
[Unit]
Description=Desa Kalimati Queue Worker
After=network.target

[Service]
User=www-data
Group=www-data
Restart=always
ExecStart=/usr/bin/php /var/www/desa-kalimati/artisan queue:work --sleep=3 --tries=3 --max-time=3600

[Install]
WantedBy=multi-user.target
==================================================

### B. Laravel Cron Scheduler (/etc/cron.d/kalimati-scheduler)
==================================================
* * * * * www-data cd /var/www/desa-kalimati && php artisan schedule:run >> /dev/null 2>&1
==================================================

---

## 4. Cloudflare Tunnel Setup (Alternative Network Security)

Jika server menggunakan Cloudflare Tunnel (tanpa membuka port 80/443 publik):

1. Install cloudflared CLI di VPS:
   curl -L [https://pkg.cloudflare.com/cloudflared-stable-linux-amd64.deb](https://pkg.cloudflare.com/cloudflared-stable-linux-amd64.deb) -o cloudflared.deb && sudo dpkg -i cloudflared.deb
2. Authenticate & Create Tunnel:
   cloudflared tunnel login
   cloudflared tunnel create desa-kalimati-tunnel
3. Konfigurasi ~/.cloudflared/config.yml:
   ==================================================
   tunnel: <TUNNEL_UUID>
   credentials-file: /root/.cloudflared/<TUNNEL_UUID>.json

   ingress:
     - hostname: kalimati.desa.id
       service: http://localhost:80
     - service: http_status:404
   ==================================================
4. Jalankan sebagai Service:
   cloudflared service install
   systemctl start cloudflared

---

## 5. Automated Deployment Script (deploy.sh)

Buat file deploy.sh di root repository untuk mempermudah eksekusi update otomatis dari GitHub/GitLab:

FILE: deploy.sh
==================================================
#!/bin/bash
set -e

echo "🚀 Starting Deployment for Website Desa Kalimati..."

# 1. Enable Maintenance Mode
php artisan down || true

# 2. Pull Latest Changes
git pull origin main

# 3. Install/Update PHP Dependencies
composer install --no-dev --optimize-autoloader --no-interaction

# 4. Run Migrations
php artisan migrate --force

# 5. Clear & Rebuild Cache
php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache
php artisan icons:cache
php artisan filament:cache-components

# 6. Build Frontend Assets
npm install
npm run build

# 7. Restart Queue Workers
php artisan queue:restart

# 8. Disable Maintenance Mode
php artisan up

echo "✅ Deployment Successfully Completed!"
==================================================
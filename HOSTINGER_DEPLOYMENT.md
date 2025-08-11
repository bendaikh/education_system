# 🚀 Hostinger Deployment Guide for EduConnect

## Prerequisites
- Git repository (GitHub/GitLab)
- Hostinger hosting with SSH access
- Domain pointing to Hostinger

## Step 1: Push to GitHub

```bash
# Initialize git (if not already done)
git init
git add .
git commit -m "Initial commit - EduConnect Laravel App"

# Add your GitHub repository
git remote add origin https://github.com/yourusername/your-repo-name.git
git branch -M main
git push -u origin main
```

## Step 2: Enable SSH on Hostinger

1. **Go to Hostinger hPanel**
2. **Navigate to Advanced → SSH Access**
3. **Enable SSH access**
4. **Note down your SSH details:**
   - Host: your-domain.com or IP
   - Port: 65002 (usually)
   - Username: your hosting username

## Step 3: Connect via SSH

```bash
# Connect to your Hostinger server
ssh username@your-domain.com -p 65002

# Or use the IP if domain isn't working yet
ssh username@IP_ADDRESS -p 65002
```

## Step 4: Clone Repository on Server

```bash
# Navigate to your domain folder (one level above public_html)
cd domains/your-domain.com/

# Clone your repository
git clone https://github.com/yourusername/your-repo-name.git .

# Or if you want it in a subfolder:
# git clone https://github.com/yourusername/your-repo-name.git laravel-app
```

## Step 5: Set Up Laravel on Hostinger

```bash
# Install Composer dependencies
composer install --no-dev --optimize-autoloader

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Install NPM dependencies and build
npm ci
npm run build

# Set permissions
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
```

## Step 6: Configure Web Root

**Option A: Move public contents to public_html**
```bash
# Move Laravel public folder contents to public_html
cp -r public/* ../public_html/
rm -rf ../public_html/index.php
cp public/index.php ../public_html/
```

Then edit `public_html/index.php` and update paths:
```php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
```

**Option B: Create redirect in public_html**
Create `.htaccess` in public_html:
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ laravel-app/public/$1 [L]
</IfModule>
```

## Step 7: Database Setup

1. **Create database in Hostinger hPanel**
2. **Update .env file:**
```env
DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=your_db_name
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password
```

3. **Run migrations:**
```bash
php artisan migrate
php artisan db:seed
```

## Step 8: Future Deployments

Make the deploy script executable:
```bash
chmod +x deploy.sh
```

For future updates:
```bash
# Just run the deployment script
./deploy.sh
```

## Troubleshooting

### 403 Forbidden Error
```bash
# Check file permissions
chmod -R 755 storage/ bootstrap/cache/
chmod 644 .env

# Check if mod_rewrite is enabled
# Contact Hostinger support if needed
```

### Composer Not Found
```bash
# Hostinger usually has composer installed globally
which composer

# If not found, download composer
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
```

### Node/NPM Issues
```bash
# Check Node version (should be 16+)
node --version

# If issues, use Hostinger's Node version manager
# Or build assets locally and commit the build folder
```

## Production Optimizations

```bash
# Cache everything for better performance
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Set up queue workers (if needed)
php artisan queue:work --daemon

# Set up cron jobs for scheduled tasks
# Add to crontab: * * * * * php /path/to/artisan schedule:run
```

## Security Checklist

- [ ] APP_DEBUG=false in production
- [ ] Strong APP_KEY generated
- [ ] Database credentials secure
- [ ] .env file not accessible via web
- [ ] File permissions set correctly
- [ ] HTTPS enabled (SSL certificate)
- [ ] Regular backups configured

## Support

If you encounter issues:
1. Check Hostinger error logs
2. Verify PHP version (8.1+ required)
3. Ensure all extensions are installed
4. Contact Hostinger support for server-specific issues

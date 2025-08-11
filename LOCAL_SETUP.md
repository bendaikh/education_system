# 🏠 Local Development Setup

## How This Works

This project is configured to work both locally and in production:

- **Locally**: Laravel runs from the `public` folder (standard)
- **Production**: Laravel runs from the root directory (Hostinger compatible)

The `index.php` file automatically detects which environment it's in and adjusts paths accordingly.

## Local Development

```bash
# Start the development server
php artisan serve

# Your local site will be available at:
# http://localhost:8000
```

## Building Assets

```bash
# Install dependencies
npm install

# Build for development
npm run dev

# Build for production
npm run build
```

## Deployment to Hostinger

```bash
# Push your changes
git add .
git commit -m "Your changes"
git push origin main

# SSH to Hostinger and run
./deploy.sh
```

## File Structure

```
project-root/
├── index.php (smart file - works locally & production)
├── .htaccess (production-ready)
├── public/
│   ├── index.php (original Laravel file)
│   └── .htaccess (original Laravel file)
├── app/
├── resources/
└── ...other Laravel files
```

## Key Features

- ✅ Works locally with `php artisan serve`
- ✅ Works in production on Hostinger
- ✅ Smart path detection
- ✅ Automated deployment
- ✅ No manual file moving required

# GoDaddy cPanel Deployment Instructions

## Prerequisites
- cPanel access with File Manager and MySQL Database access
- SSH access (if available) or Terminal in cPanel
- FTP/SFTP client (optional)

## Step 1: Backup Current Production Data

### 1.1 Export Current Database

#### Option A: Using phpMyAdmin (Recommended for cPanel)
1. **Access phpMyAdmin**:
   - Login to your cPanel
   - Find and click on "phpMyAdmin" in the Databases section

2. **Select Your Database**:
   - Click on your database name from the left sidebar
   - You should see all your tables (custom_orders, admins, etc.)

3. **Export Database**:
   - Click the "Export" tab at the top
   - Choose "Quick" export method for simple backup
   - Or choose "Custom" for more options:
     - Format: SQL
     - Tables: Select all tables or specific ones
     - Check "Add CREATE DATABASE / USE statement"
     - Check "Add AUTO_INCREMENT value"
   - Click "Go" to download the .sql file

4. **Save the Backup**:
   - Save the file as `production_backup_YYYYMMDD_HHMMSS.sql`
   - Store it in a safe location on your computer

#### Option B: Command Line (if SSH access available)
```bash
# Create backup with timestamp
mysqldump -u [username] -p [database_name] > backup_$(date +%Y%m%d_%H%M%S).sql

# Example with actual values:
mysqldump -u your_db_user -p your_db_name > backup_20241228_143000.sql
```

#### Option C: cPanel Database Backup (if available)
1. In cPanel, look for "Backup" or "Backup Wizard"
2. Choose "Download a MySQL Database Backup"
3. Select your database and download the .sql.gz file

### 1.2 Backup Current Files
- Create a backup of your current `public_html` directory
- Especially backup `/storage/app/public/custom_orders/` (customer uploaded images)

## Step 2: Upload New Code

### 2.1 Upload Files via File Manager or FTP
1. Upload all project files to a temporary directory (e.g., `ctsite_new/`)
2. **Exclude** these directories from upload:
   - `node_modules/`
   - `.git/`
   - `vendor/` (will be regenerated)
   - `storage/logs/`
   - `bootstrap/cache/`

### 2.2 File Structure Setup
```bash
# Your cPanel structure should be:
/public_html/
  index.php (Laravel's public/index.php)
  .htaccess
  /assets/ (from public/assets/)
  /build/ (from public/build/)
  /css/
  /js/
  /storage/ -> ../storage/app/public (symbolic link)

/ctsite/ (Laravel application root)
  /app/
  /config/
  /database/
  /resources/
  /routes/
  /storage/
  # ... other Laravel directories
```

## Step 3: Environment Configuration

### 3.1 Create .env File
Copy `.env.example` to `.env` and configure:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_production_database_name
DB_USERNAME=your_database_username
DB_PASSWORD=your_database_password

# SMS Configuration (keep your existing values)
TWILIO_SID=your_twilio_sid
TWILIO_TOKEN=your_twilio_token
TWILIO_FROM=your_twilio_phone

SIMPLETEXTING_API_KEY=your_simpletexting_key
SIMPLETEXTING_PHONE=your_simpletexting_phone

# Mail configuration (keep your existing values)
MAIL_MAILER=smtp
# ... your mail settings

# Session and cache (for production)
SESSION_DRIVER=file
CACHE_DRIVER=file
QUEUE_CONNECTION=database
```

## Step 4: Composer Dependencies

### 4.1 Install PHP Dependencies
```bash
# In your Laravel root directory
composer install --no-dev --optimize-autoloader
```

### 4.2 Generate Application Key
```bash
php artisan key:generate
```

## Step 5: Database Migration and Seeding

### 5.1 Run Migrations
```bash
# Run new CRM migrations
php artisan migrate --force

# Check migration status
php artisan migrate:status
```

### 5.2 Seed CRM Data
```bash
# Seed initial CRM data from historical orders
php artisan crm:seed

# Alternative: Import from CSV if you have the files
php artisan crm:import
```

### 5.3 Process Existing Confirmed Orders
```bash
# First, do a dry run to see what orders will be processed
php artisan crm:process-existing-orders --dry-run

# Process the existing confirmed orders into CRM
php artisan crm:process-existing-orders

# Check the results in CRM Dashboard
```

## Step 6: Storage and Permissions

### 6.1 Set Up Storage Link
```bash
php artisan storage:link
```

### 6.2 Set Permissions (if using SSH)
```bash
# Make sure these directories are writable
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
```

### 6.3 Copy Uploaded Images
```bash
# Copy existing customer order images if they exist
# From: old_site/storage/app/public/custom_orders/
# To: new_site/storage/app/public/custom_orders/
```

## Step 7: Cache and Optimization

### 7.1 Clear and Optimize Caches
```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 7.2 Queue Setup (Optional)
```bash
# If using database queues, create the jobs table
php artisan queue:table
php artisan migrate

# Test queue processing
php artisan queue:work --once
```

## Step 8: Final Verification

### 8.1 Test Critical Functions
1. **Admin Login**: Visit `/admin` and login
2. **CRM Dashboard**: Verify CRM data loaded correctly
3. **Order Management**: Check existing orders display properly
4. **New Order Submission**: Test the customer order form
5. **SMS Integration**: Test order confirmation workflow
6. **Email Notifications**: Verify emails are sending

### 8.2 CRM System Verification
```bash
# Test CRM integration with a sample order
php artisan crm:test-integration --create-sample

# Recalculate CRM dates if needed
php artisan crm:update-dates

# Check CRM data integrity
php artisan crm:recalculate-occasions
```

## Step 9: DNS and SSL

### 9.1 Update DNS (if needed)
- Point your domain to the new hosting location
- Verify SSL certificate is working

### 9.2 Force HTTPS
Add to `.htaccess` in public directory:
```apache
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

## Step 10: Post-Deployment Monitoring

### 10.1 Error Monitoring
- Check `storage/logs/laravel.log` for any errors
- Monitor CRM integration logs
- Verify SMS and email notifications

### 10.2 Performance Check
- Test page load times
- Verify asset loading (CSS, JS, images)
- Check database query performance

## Troubleshooting Common Issues

### Issue: 500 Internal Server Error
```bash
# Check error logs
tail -f storage/logs/laravel.log

# Common fixes:
php artisan cache:clear
php artisan config:clear
chmod -R 775 storage/
```

### Issue: CRM Data Not Loading
```bash
# Re-run CRM seeding
php artisan crm:seed --force

# Process existing orders again
php artisan crm:process-existing-orders --dry-run
php artisan crm:process-existing-orders
```

### Issue: Storage Link Not Working
```bash
# Remove and recreate storage link
rm public/storage
php artisan storage:link

# Or manually create symbolic link in cPanel File Manager
```

### Issue: Database Connection Error
- Verify database credentials in `.env`
- Check database server name (might be different in production)
- Ensure database user has all necessary privileges

## Quick Commands Reference

```bash
# Essential deployment commands
composer install --no-dev --optimize-autoloader
php artisan key:generate
php artisan migrate --force
php artisan crm:seed
php artisan crm:process-existing-orders --dry-run
php artisan crm:process-existing-orders
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Verification commands
php artisan crm:test-integration --create-sample
php artisan queue:work --once
tail -f storage/logs/laravel.log
```

## Important Notes

1. **Always backup before deployment**
2. **Test the dry-run commands first**
3. **Monitor logs after deployment**
4. **The CRM system will process existing orders and create customer profiles automatically**
5. **SMS and email integration will work with existing confirmed orders**
6. **The new admin navigation will redirect to CRM Dashboard by default**

---

## Post-Deployment: Processing Your 10 Existing Orders

After successful deployment, run these commands to integrate your existing confirmed orders:

```bash
# Step 1: Check what orders will be processed
php artisan crm:process-existing-orders --dry-run

# Step 2: Process them into the CRM system
php artisan crm:process-existing-orders

# Step 3: Verify the results
# Visit your CRM Dashboard to see the new customer profiles and occasions
```

This will automatically:
- Create customer profiles from the 10 confirmed orders
- Extract occasion data from order messages (birthdays, anniversaries, etc.)
- Set up future occasion reminders
- Update customer metrics and favorite flavors

The deployment process preserves all your existing data while adding the new CRM functionality.
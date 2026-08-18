# Migration Guide — Local to Live Hosting

Step-by-step guide to migrate the 11AA Real Estate site from Docker localhost to production.

---

## Pre-Migration Checklist

- [ ] Production hosting account is active (PHP 7.4+, MySQL 8.0+, WordPress 5.9+)
- [ ] Domain name is configured with DNS pointing to production server
- [ ] SSL certificate is installed or will be installed after migration
- [ ] All local development features are tested and working
- [ ] Full local backup is taken before starting migration

---

## Step 1: Export the Database

### Option A: phpMyAdmin (Docker)

1. Open phpMyAdmin at `http://localhost:8081`
2. Login with:
   - Server: `db`
   - Username: `realestate_admin`
   - Password: `realestate_pass_2026`
3. Select the `realestate_wp` database
4. Click the **Export** tab
5. Select **Custom** export method
6. Check:
   - Format: `SQL`
   - Output: `Save to file`
   - Check `Add DROP TABLE`, `Add CREATE TABLE`, `Add CREATE DATABASE`
7. Click **Go** to download the `.sql` file

### Option B: WP-CLI (Docker)

```bash
docker exec realestate_wordpress wp db export /var/www/html/backup.sql --all-tablespaces
docker cp realestate_wordpress:/var/www/html/backup.sql ./backup.sql
```

### Option C: mysqldump

```bash
docker exec realestate_mysql mysqldump -u realestate_admin -prealestate_pass_2026 realestate_wp > backup.sql
```

---

## Step 2: Download wp-content

### From Docker Volume

```bash
mkdir backup-wp-content
docker cp realestate_wordpress:/var/www/html/wp-content ./backup-wp-content/wp-content
```

### Exclude Unnecessary Files

```bash
rm -f backup-wp-content/wp-content/object-cache.php
rm -f backup-wp-content/wp-content/debug.log
```

### Expected Structure

```
wp-content/
├── plugins/
│   ├── realestate-core/
│   ├── realestate-enquiry/
│   ├── realestate-submit-property/
│   ├── realestate-datetime/
│   ├── realestate-weather/
│   └── realestate-analytics/
├── themes/
│   ├── astra/
│   └── realestate-child/
├── uploads/
└── languages/
```

---

## Step 3: Create the Production Database

### Via Hosting Control Panel (cPanel/Plesk)

1. Go to **MySQL Databases**
2. Create a new database (e.g., `youruser_realestate`)
3. Create a new database user with a strong password
4. Grant ALL PRIVILEGES on the new database to the user
5. Note down: database name, username, password

### Via SSH/CLI

```sql
CREATE DATABASE youruser_realestate CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'youruser_realestate'@'localhost' IDENTIFIED BY 'YourStrongPassword2026!';
GRANT ALL PRIVILEGES ON youruser_realestate.* TO 'youruser_realestate'@'localhost';
FLUSH PRIVILEGES;
```

---

## Step 4: Upload Files

### Upload wp-content

Via **cPanel File Manager**, **FTP/SFTP**, or **SSH/SCP**:

```bash
scp -r backup-wp-content/wp-content/ user@server:/var/www/html/wp-content/
```

### Upload Database Dump

Upload `backup.sql` to the production server.

---

## Step 5: Import the Database

### Via phpMyAdmin

1. Open phpMyAdmin on production
2. Select the new database
3. Click **Import** tab
4. Choose `backup.sql`
5. Click **Go**

### Via SSH/CLI

```bash
mysql -u youruser_realestate -p youruser_realestate < backup.sql
```

---

## Step 6: Update wp-config.php

Edit `wp-config.php` on the production server with new database credentials:

```php
define( 'DB_NAME', 'youruser_realestate' );
define( 'DB_USER', 'youruser_realestate' );
define( 'DB_PASSWORD', 'YourStrongPassword2026!' );
define( 'DB_HOST', 'localhost' );
define( 'DB_CHARSET', 'utf8mb4' );
define( 'DB_COLLATE', '' );
```

Update the table prefix if different:

```php
$table_prefix = 'wp_';
```

Update authentication keys and salts (generate new ones at https://api.wordpress.org/secret-key/1.1/salt/):

```php
define( 'AUTH_KEY',         'your-new-key-here' );
define( 'SECURE_AUTH_KEY',  'your-new-key-here' );
define( 'LOGGED_IN_KEY',    'your-new-key-here' );
define( 'NONCE_KEY',        'your-new-key-here' );
define( 'AUTH_SALT',        'your-new-key-here' );
define( 'SECURE_AUTH_SALT', 'your-new-key-here' );
define( 'LOGGED_IN_SALT',   'your-new-key-here' );
define( 'NONCE_SALT',       'your-new-key-here' );
```

Enable production debug settings:

```php
define( 'WP_DEBUG', false );
define( 'WP_DEBUG_LOG', false );
define( 'WP_DEBUG_DISPLAY', false );
```

---

## Step 7: Update Site URLs (Search and Replace)

### Option A: WP-CLI

```bash
wp search-replace 'http://localhost:8080' 'https://www.yoursite.com' --all-tables --dry-run
wp search-replace 'http://localhost:8080' 'https://www.yoursite.com' --all-tables
```

### Option B: phpMyAdmin SQL

```sql
UPDATE wp_options SET option_value = replace(option_value, 'http://localhost:8080', 'https://www.yoursite.com') WHERE option_name IN ('siteurl', 'home');

UPDATE wp_posts SET guid = replace(guid, 'http://localhost:8080', 'https://www.yoursite.com');
UPDATE wp_posts SET post_content = replace(post_content, 'http://localhost:8080', 'https://www.yoursite.com');
UPDATE wp_postmeta SET meta_value = replace(meta_value, 'http://localhost:8080', 'https://www.yoursite.com');
```

### Option C: Plugin

Use **Better Search Replace** plugin:
- Search: `http://localhost:8080`
- Replace: `https://www.yoursite.com`
- Select all tables
- Run as dry-run first

---

## Step 8: SSL/HTTPS Configuration

### Install SSL Certificate

1. **Free SSL**: Use Let's Encrypt via hosting control panel or Certbot
2. **Paid SSL**: Install certificate from your provider

### Force HTTPS

Add to `.htaccess`:

```apache
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

Or update `wp-config.php`:

```php
if ( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ) {
    $_SERVER['HTTPS'] = 'on';
}
```

### Update WordPress URLs

After SSL is working, ensure both URLs use HTTPS:

```php
define( 'WP_HOME', 'https://www.yoursite.com' );
define( 'WP_SITEURL', 'https://www.yoursite.com' );
```

---

## Step 9: Email Configuration on Production

Install an SMTP plugin (WP Mail SMTP or similar) and configure:

| Setting | Value |
|---------|-------|
| From Email | `noreply@yoursite.com` |
| From Name | `11AA Real Estate` |
| SMTP Host | Your hosting SMTP or SendGrid/Mailgun |
| Encryption | TLS |
| Port | 587 |
| Authentication | Yes |

### Test Emails

1. Submit an enquiry form on the live site
2. Check admin inbox for notification
3. Check customer inbox for auto-reply

---

## Step 10: Cron Jobs Setup

### WordPress Cron

WordPress uses `wp-cron.php` triggered by page visits. For reliable scheduling:

Disable the page-load cron in `wp-config.php`:

```php
define( 'DISABLE_WP_CRON', true );
```

Add a server cron job (via cPanel or SSH):

```bash
# Run every 5 minutes
*/5 * * * * wget -q -O - https://www.yoursite.com/wp-cron.php?doing_wp_cron > /dev/null 2>&1
```

### Cleanup Cron

The analytics plugin schedules `realestate_analytics_cleanup`. Verify it is running:

```bash
wp cron event list
```

---

## Step 11: File Permissions

Set correct file permissions on the production server:

```bash
# Directories: 755
find /var/www/html -type d -exec chmod 755 {} \;

# Files: 644
find /var/www/html -type f -exec chmod 644 {} \;

# wp-config.php: 400 or 440
chmod 400 /var/www/html/wp-config.php

# .htaccess: 644
chmod 644 /var/www/html/.htaccess

# wp-content/uploads: 755
chmod -R 755 /var/www/html/wp-content/uploads
```

---

## Step 12: Post-Migration Testing

### Functional Testing

- [ ] Homepage loads with hero section, search, properties, stats
- [ ] All navigation links work
- [ ] Property search returns results
- [ ] Property detail pages load correctly
- [ ] Enquiry form submits and sends emails
- [ ] Submit property form works with file uploads
- [ ] Weather widget displays current data
- [ ] Date/time widget shows correct timezone
- [ ] Visitor counter increments
- [ ] 404 page displays for invalid URLs

### Performance Testing

- [ ] Page load time under 3 seconds
- [ ] Images load with lazy loading
- [ ] CSS/JS minification enabled
- [ ] GZIP compression enabled

### SEO Testing

- [ ] XML sitemap accessible at `/sitemap.xml`
- [ ] Robots.txt is configured
- [ ] Meta titles and descriptions present
- [ ] Canonical URLs correct

### Security Testing

- [ ] Nonce verification working on forms
- [ ] File upload validation active
- [ ] Admin access restricted to authorized users
- [ ] Login page secure

---

## Step 13: Performance Optimization for Production

### Caching Plugin

Install **WP Super Cache** or **W3 Total Cache**:

- Enable page caching
- Enable browser caching
- Enable GZIP compression

### Image Optimization

- Use WebP format for property images
- Enable lazy loading (already in the theme via `loading="lazy"`)
- Compress existing images with ShortPixel or Imagify

### CDN Setup

For high-traffic sites, configure a CDN (Cloudflare, BunnyCDN):

1. Create CDN account
2. Update DNS to point to CDN
3. Configure cache rules for static assets

### Database Optimization

```bash
wp db optimize
```

Remove post revisions and transients:

```bash
wp post delete $(wp post list --post_type=revision --format=ids)
wp transient delete --all
```

---

## Rollback Plan

If migration fails:

1. Revert `wp-config.php` to local database credentials
2. Restore the database from the backup `.sql` file
3. Verify local site still works at `http://localhost:8080`
4. Diagnose and fix issues before re-attempting migration

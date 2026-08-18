# Backup Procedures — 11AA Real Estate WordPress Project

Regular backups are essential. Follow these procedures to back up and restore the site.

---

## Full Site Backup (Files + Database)

### Manual Full Backup

#### Step 1: Export Database

```bash
# Via Docker
docker exec realestate_mysql mysqldump -u realestate_admin -prealestate_pass_2026 realestate_wp > backup-$(date +%Y%m%d).sql
```

Or via phpMyAdmin:

1. Open `http://localhost:8081`
2. Select `realestate_wp` database
3. Click **Export** > **Custom** > **Go**

#### Step 2: Copy Files

```bash
# Backup wp-content
docker cp realestate_wordpress:/var/www/html/wp-content ./backup-$(date +%Y%m%d)-files/
```

#### Step 3: Create Archive

```bash
# Windows
Compress-Archive -Path .\backup-20260818* -DestinationPath backup-full-20260818.zip

# Linux/Mac
tar -czf backup-full-$(date +%Y%m%d).tar.gz backup-20260818.sql backup-20260818-files/
```

### Automated Full Backup Script

Create `backup-full.sh`:

```bash
#!/bin/bash
BACKUP_DIR="/backups/$(date +%Y%m%d-%H%M%S)"
mkdir -p "$BACKUP_DIR"

# Database
docker exec realestate_mysql mysqldump -u realestate_admin -prealestate_pass_2026 realestate_wp > "$BACKUP_DIR/database.sql"

# Files
docker cp realestate_wordpress:/var/www/html/wp-content "$BACKUP_DIR/wp-content"

# Compress
tar -czf "$BACKUP_DIR.tar.gz" -C "$(dirname $BACKUP_DIR)" "$(basename $BACKUP_DIR)"
rm -rf "$BACKUP_DIR"

# Keep last 7 backups
ls -dt /backups/backup-full-*.tar.gz | tail -n +8 | xargs rm -f

echo "Backup completed: $BACKUP_DIR.tar.gz"
```

Schedule with cron (daily at 2 AM):

```bash
0 2 * * * /path/to/backup-full.sh >> /var/log/backup.log 2>&1
```

---

## Database-Only Backup

### Via WP-CLI

```bash
docker exec realestate_wordpress wp db export /backups/db-$(date +%Y%m%d).sql
```

### Via phpMyAdmin

1. Open phpMyAdmin
2. Select `realestate_wp`
3. **Export** > **Quick** > **SQL** > **Go**

### Via mysqldump

```bash
docker exec realestate_mysql mysqldump -u realestate_admin -prealestate_pass_2026 --single-transaction realestate_wp > db-backup.sql
```

---

## wp-content Backup

### What to Back Up

| Folder | Priority | Contents |
|--------|----------|----------|
| `plugins/` | Critical | All 6 custom plugins |
| `themes/` | Critical | Astra + realestate-child |
| `uploads/` | High | Property images, media |
| `languages/` | Low | Translation files |
| `mu-plugins/` | Low | Must-use plugins |

### What NOT to Back Up

- `cache/` — Regenerated automatically
- `debug.log` — Development only
- `upgrade/` — Temporary files

### Backup Command

```bash
docker cp realestate_wordpress:/var/www/html/wp-content/plugins ./backup-plugins
docker cp realestate_wordpress:/var/www/html/wp-content/themes ./backup-themes
docker cp realestate_wordpress:/var/www/html/wp-content/uploads ./backup-uploads
```

---

## Automated Backup Recommendations

### Recommended Schedule

| Backup Type | Frequency | Retention |
|-------------|-----------|-----------|
| Full site | Daily | 7 days |
| Database only | Every 6 hours | 3 days |
| wp-content | Daily | 7 days |
| Before updates | Manual | Until confirmed working |

### Cloud Backup Options

| Provider | Free Tier | Recommended For |
|----------|-----------|-----------------|
| UpdraftPlus | 1GB free | Small sites |
| BlogVault | Daily offsite | Managed backups |
| Manual S3 | 5GB free | Developer-managed |
| Hosting backup | Varies | Most shared hosts |

### UpdraftPlus Configuration

1. Install **UpdraftPlus** plugin
2. Go to **Settings > UpdraftPlus Backups**
3. Configure:

| Setting | Value |
|---------|-------|
| Files backup schedule | Daily |
| Database backup schedule | Daily |
| Retain backups | 2 |
| Remote storage | Google Drive / Dropbox / S3 |

---

## Backup Storage Best Practices

### The 3-2-1 Rule

- **3** copies of data
- **2** different storage types
- **1** offsite (cloud)

### Storage Locations

| Location | Type | Purpose |
|----------|------|---------|
| Local machine | Local | Quick restore |
| External drive | Portable | Offline copy |
| Cloud (S3/GDrive) | Remote | Disaster recovery |

### Security

- Encrypt backups containing user data
- Use strong passwords for backup storage
- Never store backups in publicly accessible directories
- Verify backup integrity periodically

---

## Restore Procedures

### Restore from Full Backup

#### Step 1: Restore Database

```bash
# Via Docker
docker exec -i realestate_mysql mysql -u realestate_admin -prealestate_pass_2026 realestate_wp < backup-full/database.sql
```

Or via phpMyAdmin:

1. Select database
2. Click **Import**
3. Choose backup `.sql` file
4. Click **Go**

#### Step 2: Restore Files

```bash
docker cp backup-full/wp-content/. realestate_wordpress:/var/www/html/wp-content/
docker-compose restart wordpress
```

#### Step 3: Verify

1. Visit `http://localhost:8080`
2. Check all pages load
3. Test property search
4. Submit a test enquiry

### Restore Database Only

```bash
docker exec -i realestate_mysql mysql -u realestate_admin -prealestate_pass_2026 realestate_wp < db-backup.sql
```

### Restore wp-content Only

```bash
docker cp backup-plugins/. realestate_wordpress:/var/www/html/wp-content/plugins/
docker cp backup-themes/. realestate_wordpress:/var/www/html/wp-content/themes/
docker cp backup-uploads/. realestate_wordpress:/var/www/html/wp-content/uploads/
docker-compose restart wordpress
```

### Flush After Restore

```bash
docker exec realestate_wordpress wp cache flush
docker exec realestate_wordpress wp rewrite flush
```

---

## Production Backup on Hosting

### cPanel Backup

1. Log in to cPanel
2. Go to **Backup** or **Backup Wizard**
3. Select **Full Backup** or **Database Only**
4. Download to local machine

### SSH Backup (Production)

```bash
ssh user@your-server
mysqldump -u db_user -p db_name > ~/backup-$(date +%Y%m%d).sql
tar -czf ~/wp-content-backup-$(date +%Y%m%d).tar.gz /var/www/html/wp-content/
```

### Download to Local

```bash
scp user@your-server:~/backup-*.sql ./
scp user@your-server:~/wp-content-backup-*.tar.gz ./
```

---

## Pre-Update Backup Reminder

**Always create a backup before:**

- [ ] Updating WordPress core
- [ ] Updating plugins
- [ ] Updating themes
- [ ] Making code changes
- [ ] Migrating to new hosting
- [ ] Changing server configuration

Quick backup command:

```bash
docker exec realestate_mysql mysqldump -u realestate_admin -prealestate_pass_2026 realestate_wp > pre-update-$(date +%Y%m%d-%H%M).sql
```

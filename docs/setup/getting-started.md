# Getting Started — 11AA Real Estate WordPress Project

This guide walks you through setting up the local development environment from scratch.

---

## Prerequisites

Install the following before proceeding:

| Software | Version | Download |
|----------|---------|----------|
| **Docker Desktop** | Latest | https://www.docker.com/products/docker-desktop |
| **VS Code** | Latest | https://code.visualstudio.com |
| **Git** | Latest | https://git-scm.com |

Verify installations:

```bash
docker --version
git --version
code --version
```

---

## Step 1: Clone / Copy the Project

Copy the project folder to your local machine:

```
D:\11AA WP RealEstate\
├── child-theme/
├── docker/
├── docs/
├── elementor-templates/
└── plugins/
```

---

## Step 2: Start Docker Containers

Open a terminal in the `docker/` folder:

```bash
cd D:\11AA WP RealEstate\docker
docker-compose up -d
```

This starts three containers:

| Container | Port | Purpose |
|-----------|------|---------|
| `realestate_wordpress` | **8080** | WordPress site |
| `realestate_mysql` | 3306 | MySQL 8.0 database |
| `realestate_phpmyadmin` | **8081** | Database management |

Wait approximately 30 seconds for all containers to initialize. Check status:

```bash
docker-compose ps
```

### Verify containers are healthy

```bash
docker-compose logs -f
```

Look for `ready for connections` from MySQL and WordPress.

---

## Step 3: WordPress Installation

1. Open **http://localhost:8080** in your browser
2. Select your language (English recommended)
3. Fill in the installation form:

| Field | Value |
|-------|-------|
| Site Title | `11AA Real Estate` |
| Username | `admin` |
| Password | *(choose a strong password)* |
| Your Email | `admin@example.com` |
| Search Engine Visibility | Uncheck (allow indexing) |

4. Click **Install WordPress**

### Database Connection (Automatic)

WordPress connects to MySQL automatically using the credentials in `docker/.env`:

```
Database:  realestate_wp
User:      realestate_admin
Password:  realestate_pass_2026
Host:      db:3306
```

> These credentials are for **local development only**. Change them for production.

---

## Step 4: Theme Activation

### 4a. Install Astra Parent Theme

1. Go to **Appearance > Themes > Add New**
2. Search for **Astra**
3. Click **Install**, then **Activate**

### 4b. Install the Child Theme

Copy the child theme into WordPress's themes directory:

```powershell
# From the project root
Copy-Item -Recurse -Force "D:\11AA WP RealEstate\child-theme\realestate-child" `
  "C:\Users\<your-username>\docker-desktop\volumes\realestate_wordpress\_data\var\www\html\wp-content\themes\realestate-child"
```

Alternatively, mount the child theme by adding a volume in `docker-compose.yml`:

```yaml
volumes:
  - ./child-theme/realestate-child:/var/www/html/wp-content/themes/realestate-child
```

Then restart:

```bash
docker-compose restart wordpress
```

### 4c. Activate the Child Theme

1. Go to **Appearance > Themes**
2. Find **Real Estate Child Theme**
3. Click **Activate**

The child theme inherits all Astra styles and adds custom CSS, templates, and scripts.

---

## Step 5: Plugin Installation and Activation

Copy all plugin folders into the WordPress plugins directory:

```powershell
Copy-Item -Recurse -Force "D:\11AA WP RealEstate\plugins\*" `
  "C:\Users\<your-username>\docker-desktop\volumes\realestate_wordpress\_data\var\www\html\wp-content\plugins\"
```

Or mount plugins as a volume in `docker-compose.yml`:

```yaml
volumes:
  - ./plugins:/var/www/html/wp-content/plugins
```

### Activate All Plugins

Go to **Plugins > Installed Plugins** and activate in this order:

| Order | Plugin | Purpose |
|-------|--------|---------|
| 1 | **11AA Real Estate Core** | Property CPT, search, meta boxes, widgets, email |
| 2 | **11AA Real Estate Enquiries** | Enquiry form and storage |
| 3 | **11AA Real Estate Submit Property** | Public property submission form |
| 4 | **11AA Real Estate DateTime** | Live date/time display |
| 5 | **11AA Real Estate Weather** | Weather widget (OpenWeatherMap) |
| 6 | **11AA Real Estate Analytics** | Visitor tracking and statistics |

---

## Step 6: Initial Configuration

### 6a. General Settings

Go to **Settings > General**:

| Setting | Value |
|---------|-------|
| Site Title | `11AA Real Estate` |
| Tagline | `Premium Real Estate Solutions` |
| Timezone | `(GMT+05:30) Colombo` |
| Date Format | `F j, Y` |
| Time Format | `g:i a` |
| Language | `English` |

### 6b. Permalinks

Go to **Settings > Permalinks**:

- Select **Post name** (`/%postname%/`)
- Click **Save Changes**

### 6c. Reading Settings

Go to **Settings > Reading**:

- **Your homepage displays**: A static page
- **Homepage**: Select the page titled **Home** (created from `front-page.php`)
- **Posts page**: Select **Blog** or leave blank

### 6d. Weather API Key

1. Get a free API key from [OpenWeatherMap](https://openweathermap.org/api)
2. Go to **Settings > Weather Settings** (added by the Weather plugin)
3. Enter your API key
4. Set Location: `Colombo,LK`
5. Set Unit: `Celsius (C)`
6. Click **Test Connection** to verify
7. Click **Save Settings**

### 6e. Create Pages

Create the following WordPress pages:

| Page | Slug | Template |
|------|------|----------|
| Home | `home` | Front Page (`front-page.php`) |
| Properties | `properties` | Default |
| About Us | `about` | Default |
| Services | `services` | Default |
| Contact | `contact` | Default |
| Submit Property | `submit-property` | Default |
| Blog | `blog` | Default |

Then set **Home** as the front page in Settings > Reading.

---

## Step 7: First Login and Setup

### Access WordPress Admin

- **Admin URL**: http://localhost:8080/wp-admin/
- **phpMyAdmin**: http://localhost:8081

### phpMyAdmin Login

| Field | Value |
|-------|-------|
| Server | `db` |
| Username | `realestate_admin` |
| Password | `realestate_pass_2026` |

### Create a Menu

1. Go to **Appearance > Menus**
2. Create a new menu called **Primary Menu**
3. Add pages: Home, Properties, About, Services, Contact
4. Set as **Primary Menu** location

### Add Widgets

Go to **Appearance > Widgets** and configure:

- **Footer Column 1**: Company info
- **Footer Column 2**: Quick links
- **Footer Column 3**: Services
- **Footer Column 4**: Contact / Newsletter

---

## Useful Docker Commands

```bash
# Start containers
docker-compose up -d

# Stop containers
docker-compose down

# View logs
docker-compose logs -f

# Restart WordPress only
docker-compose restart wordpress

# Stop and DELETE all data (careful!)
docker-compose down -v

# Access WordPress container shell
docker exec -it realestate_wordpress bash

# Access MySQL
docker exec -it realestate_mysql mysql -u realestate_admin -p realestate_wp
```

---

## Troubleshooting

### WordPress shows "Error establishing a database connection"

- Wait 30 seconds after `docker-compose up -d`
- Check MySQL health: `docker-compose logs db`
- Verify credentials in `docker/.env` match `wp-config.php`

### Port 8080 already in use

Change the port in `docker-compose.yml`:

```yaml
ports:
  - "8090:80"  # Change 8080 to 8090
```

### Child theme not appearing

- Ensure the theme folder is at `wp-content/themes/realestate-child/`
- The `style.css` must contain the `Template: astra` header
- Restart WordPress container after adding files

### Plugins not showing

- Ensure plugins are in `wp-content/plugins/realestate-*/`
- Each plugin folder must contain its main PHP file with proper plugin headers

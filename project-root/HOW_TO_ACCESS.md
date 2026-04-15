# How to Access EvenTrix Full Source Code

## 📁 File Locations

All code is located in: `/app/project-root/`

### Quick Directory Overview
```
/app/project-root/
├── api/                    # Backend PHP files
├── public/                 # Frontend files
│   ├── pages/             # All application pages
│   ├── components/        # Reusable components
│   └── assets/            # CSS & JavaScript
├── database/              # SQL schema
├── src/                   # Tailwind source
├── README.md              # Full documentation
└── start_server.sh        # Server startup script
```

## 🎯 Quick Access Methods

### Method 1: View Individual Files
Use the file explorer or view files directly:
```bash
# View any file
cat /app/project-root/public/index.html
cat /app/project-root/api/auth.php
```

### Method 2: Copy to Your Working Directory
```bash
# Copy entire project
cp -r /app/project-root /your/destination/path/
```

### Method 3: Download Archive
```bash
# Archive is ready at:
/app/eventrix-full-source.tar.gz

# Extract it:
tar -xzf /app/eventrix-full-source.tar.gz
```

## 📂 Complete File List

### Backend API (10 files)
- `/app/project-root/api/db.php`
- `/app/project-root/api/auth.php`
- `/app/project-root/api/get_events.php`
- `/app/project-root/api/get_scores.php`
- `/app/project-root/api/submit_score.php`
- `/app/project-root/api/get_rankings.php`
- `/app/project-root/api/get_criteria.php`
- `/app/project-root/api/get_event_participants.php`
- `/app/project-root/api/participants.php`
- `/app/project-root/api/teams.php`

### Frontend Pages (15 files)
- `/app/project-root/public/index.html`
- `/app/project-root/public/login.html`
- `/app/project-root/public/register.html`
- `/app/project-root/public/pages/dashboard.html`
- `/app/project-root/public/pages/events.html`
- `/app/project-root/public/pages/participants.html`
- `/app/project-root/public/pages/criteria.html`
- `/app/project-root/public/pages/scoring.html`
- `/app/project-root/public/pages/rankings.html`
- `/app/project-root/public/pages/live-scores.html`
- `/app/project-root/public/pages/schedule.html`
- `/app/project-root/public/pages/analytics.html`
- `/app/project-root/public/pages/announcements.html`
- `/app/project-root/public/pages/users.html`
- `/app/project-root/public/pages/settings.html`

### Components (3 files)
- `/app/project-root/public/components/sidebar.html`
- `/app/project-root/public/components/navbar.html`
- `/app/project-root/public/components/footer.html`

### JavaScript (4 files)
- `/app/project-root/public/assets/js/main.js`
- `/app/project-root/public/assets/js/api.js`
- `/app/project-root/public/assets/js/live.js`
- `/app/project-root/public/assets/js/scoring.js`

### Styles
- `/app/project-root/public/assets/css/output.css`
- `/app/project-root/src/input.css`

### Configuration
- `/app/project-root/tailwind.config.js`
- `/app/project-root/start_server.sh`

### Database
- `/app/project-root/database/schema.sql`

### Documentation
- `/app/project-root/README.md`
- `/app/project-root/DEPLOYMENT_CHECKLIST.md`

## 🚀 To Use on Your Local Machine

1. **Copy/download the entire project-root folder**

2. **Setup your environment:**
   ```bash
   # Install dependencies
   - PHP 8.x
   - MySQL/MariaDB
   - Tailwind CSS (optional, CSS already compiled)
   ```

3. **Setup database:**
   ```bash
   mysql -u root -p
   CREATE DATABASE eventrix;
   USE eventrix;
   SOURCE /path/to/project-root/database/schema.sql;
   ```

4. **Update database credentials:**
   Edit `/api/db.php` with your local MySQL credentials

5. **Start server:**
   ```bash
   cd project-root/public
   php -S localhost:8090
   ```

6. **Access application:**
   - Open: http://localhost:8090/
   - Login: admin@eventrix.com / password

## 💡 Tips

- All files have proper comments and documentation
- Check README.md for full setup instructions
- Database schema includes sample data
- Server script handles startup automatically

## 📞 Questions?

Review the README.md in the project root for comprehensive documentation!

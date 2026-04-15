# EvenTrix - Multi-Event Scoring and Ranking System

**Mindoro State University (MINSU)**  
Automated Multi-Event Scoring System

## 🎯 Overview

EvenTrix is a comprehensive web-based event management and scoring system designed for multi-event competitions. It provides real-time scoring, automated ranking calculations, and role-based access control for administrators, event managers, judges, and participants.

## 🌟 Key Features

- **Automated Scoring & Ranking**: Real-time score computation with weighted criteria
- **Multi-Event Support**: Manage multiple simultaneous events
- **Live Score Display**: Auto-refreshing leaderboards (5-second polling)
- **Role-Based Access Control**: Admin, Event Manager, USG Officer, Judge, General User
- **Data Analytics**: Performance insights per game, team, and family
- **Schedule Management**: Event calendar and timeline views
- **Mobile Responsive**: Optimized for all screen sizes
- **Audit Trail**: Complete transaction logging for accountability

## 🛠️ Technology Stack

- **Frontend**: HTML5, Tailwind CSS, Vanilla JavaScript
- **Backend**: PHP 8.2
- **Database**: MySQL/MariaDB
- **Server**: PHP Built-in Development Server (Production: Apache/Nginx)

## 🎨 Design System

### Color Palette
- **Primary**: `#0f3f2e` (Dark Green - MINSU brand)
- **Accent**: `#16a34a` (Vibrant Green)
- **Soft**: `#bbf7d0` (Light Mint)
- **Light**: `#f9fafb` (Off-White)
- **Dark**: `#111827` (Near Black)

### Typography
- Font Family: Inter (sans-serif)

## 📁 Project Structure

```
/project-root
├── /public                 # Public web root
│   ├── index.html         # Landing page
│   ├── login.html         # Login page
│   ├── register.html      # Registration page
│   ├── /pages             # Application pages
│   │   ├── dashboard.html
│   │   ├── events.html
│   │   ├── participants.html
│   │   ├── criteria.html
│   │   ├── scoring.html
│   │   ├── rankings.html
│   │   ├── live-scores.html
│   │   ├── schedule.html
│   │   ├── users.html
│   │   ├── analytics.html
│   │   ├── announcements.html
│   │   └── settings.html
│   ├── /assets
│   │   ├── /css
│   │   │   └── output.css  # Compiled Tailwind CSS
│   │   └── /js
│   │       ├── main.js     # Core utilities
│   │       ├── api.js      # API wrapper
│   │       ├── live.js     # Live score polling
│   │       └── scoring.js  # Judge scoring interface
│   └── /components
│       ├── sidebar.html
│       ├── navbar.html
│       └── footer.html
├── /api                    # Backend API endpoints
│   ├── db.php             # Database connection
│   ├── auth.php           # Authentication
│   ├── get_events.php     # Fetch events
│   ├── get_scores.php     # Fetch scores
│   ├── submit_score.php   # Submit score
│   ├── get_rankings.php   # Fetch rankings
│   ├── get_criteria.php   # Fetch criteria
│   ├── get_event_participants.php
│   ├── participants.php   # Participant CRUD
│   └── teams.php          # Team CRUD
├── /database
│   └── schema.sql         # Database schema
├── /src
│   └── input.css          # Tailwind source
├── tailwind.config.js
└── start_server.sh        # Server startup script
```

## 🗄️ Database Schema

The system uses a normalized MySQL database (3NF) with 15 tables:

### Core Tables
- **Users**: System users with role-based access
- **Roles**: Admin, Event Manager, USG Officer, Judge, General User
- **Events**: Competition event management
- **Teams**: Team groupings
- **Participants**: Individual contestants
- **Judges**: Judge assignments

### Scoring Tables
- **Criteria**: Scoring criteria with weights
- **Scores**: Individual score submissions
- **ScoreDetails**: Detailed score breakdowns
- **ScoreTotals**: Precomputed rankings (performance optimization)

### Support Tables
- **EventParticipants**: Event-participant junction
- **EventJudges**: Event-judge assignments
- **Schedules**: Event timelines
- **Announcements**: System notifications
- **AuditLogs**: Complete audit trail

## 🚀 Getting Started

### Prerequisites
- PHP 8.x
- MySQL/MariaDB
- Yarn (for Tailwind CSS compilation)

### Installation

1. **Clone and Navigate**
   ```bash
   cd /app/project-root
   ```

2. **Setup Database**
   ```bash
   sudo mysql < database/schema.sql
   ```

3. **Compile Tailwind CSS**
   ```bash
   tailwindcss -i ./src/input.css -o ./public/assets/css/output.css --minify
   ```

4. **Start Server**
   ```bash
   ./start_server.sh
   ```

5. **Access Application**
   - Landing Page: http://localhost:8090/
   - Login: http://localhost:8090/login.html
   - Dashboard: http://localhost:8090/pages/dashboard.html

### Default Admin Credentials
```
Email: admin@eventrix.com
Password: password
```

## 👥 User Roles & Permissions

### Admin
- Full system access
- User management
- Event creation/deletion
- Role assignment

### Event Manager
- Create and manage events
- Assign participants and judges
- Define scoring criteria
- View reports

### USG Officer
- Monitor events
- Generate reports
- View analytics

### Judge
- Submit scores for assigned events
- View assigned criteria
- Access restricted to assigned events only

### General User
- View public event information
- View live scores
- View schedules and results

## 📊 Core Features

### Live Score System
- Auto-refresh every 5 seconds (configurable)
- Real-time ranking updates
- Weighted score calculations
- Per-event leaderboards

### Scoring Workflow
1. Judge logs in and selects assigned event
2. Selects participant/team
3. Chooses scoring criteria
4. Enters score value and optional remarks
5. System automatically:
   - Updates score totals
   - Recalculates rankings
   - Logs audit trail

### Analytics Dashboard
- Total events, participants, scores
- Performance metrics per team/family
- Highest score tracking
- Event completion statistics

## 🔒 Security Features

- Password hashing (bcrypt)
- Session-based authentication
- Role-based access control (RBAC)
- SQL injection prevention (prepared statements)
- Audit logging for all transactions
- Input validation and sanitization

## 📱 Mobile Responsive Design

- Fully responsive layout
- Touch-friendly navigation
- Optimized tables for mobile viewing
- Collapsible sidebar for small screens

## 🧪 Testing

Sample data has been pre-loaded:
- 3 Events (Basketball, Volleyball, Track and Field)
- 4 Teams (Engineering, Science, Arts, Business)
- 6 Participants
- Multiple scoring criteria

Test the system:
1. Login as admin
2. Navigate to Events
3. View live scores
4. Test scoring interface (Judge role)
5. Check analytics and reports

## 🔧 Configuration

### Database Configuration
Edit `/api/db.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'eventrix_user');
define('DB_PASS', 'eventrix_pass');
define('DB_NAME', 'eventrix');
```

### Live Score Polling Interval
Edit `/public/assets/js/live.js`:
```javascript
startLiveScorePolling(eventId, 5000); // 5 seconds
```

## 📝 API Endpoints

### Authentication
- `POST /api/auth.php?action=login`
- `POST /api/auth.php?action=register`
- `POST /api/auth.php?action=logout`
- `GET /api/auth.php?action=check`

### Events
- `GET /api/get_events.php` - Get all events
- `GET /api/get_events.php?event_id=X` - Get single event
- `GET /api/get_events.php?status=ongoing` - Filter by status

### Scoring
- `GET /api/get_scores.php?event_id=X`
- `POST /api/submit_score.php`
- `GET /api/get_rankings.php?event_id=X`

### Data Management
- `GET /api/participants.php`
- `GET /api/teams.php`
- `GET /api/get_criteria.php?event_id=X`
- `GET /api/get_event_participants.php?event_id=X`

## 🐛 Troubleshooting

### Server won't start
```bash
# Check if port is available
sudo lsof -i :8090

# Kill existing process
pkill -9 php

# Restart
./start_server.sh
```

### Database connection errors
```bash
# Check MySQL status
sudo service mariadb status

# Restart MySQL
sudo service mariadb restart
```

### CSS not loading
```bash
# Recompile Tailwind
tailwindcss -i ./src/input.css -o ./public/assets/css/output.css
```

## 🚀 Production Deployment

For production, use Apache or Nginx:

### Apache Configuration
```apache
<VirtualHost *:80>
    ServerName eventrix.yourdomain.com
    DocumentRoot /var/www/eventrix/public
    
    <Directory /var/www/eventrix/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    Alias /api /var/www/eventrix/api
</VirtualHost>
```

## 📄 License

Copyright © 2026 Mindoro State University. All rights reserved.

## 👨‍💻 Development Team

Developed for Mindoro State University  
EvenTrix v1.0 - Multi-Event Scoring System

## 📞 Support

For issues or feature requests, contact the MINSU IT Department.

---

**EvenTrix** - Powering fair and transparent event scoring at Mindoro State University

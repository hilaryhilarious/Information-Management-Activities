-- EvenTrix Database Schema
-- MySQL/MariaDB compatible

-- Drop tables if they exist (in reverse order of dependencies)
DROP TABLE IF EXISTS AuditLogs;
DROP TABLE IF EXISTS Announcements;
DROP TABLE IF EXISTS ScoreTotals;
DROP TABLE IF EXISTS ScoreDetails;
DROP TABLE IF EXISTS Scores;
DROP TABLE IF EXISTS EventJudges;
DROP TABLE IF EXISTS EventParticipants;
DROP TABLE IF EXISTS Schedules;
DROP TABLE IF EXISTS Criteria;
DROP TABLE IF EXISTS Judges;
DROP TABLE IF EXISTS Participants;
DROP TABLE IF EXISTS Teams;
DROP TABLE IF EXISTS Events;
DROP TABLE IF EXISTS Users;
DROP TABLE IF EXISTS Roles;

-- Roles table
CREATE TABLE Roles (
    role_id INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Users table
CREATE TABLE Users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    role_id INT NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES Roles(role_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Events table
CREATE TABLE Events (
    event_id INT AUTO_INCREMENT PRIMARY KEY,
    event_name VARCHAR(100) NOT NULL,
    description TEXT,
    event_type VARCHAR(50),
    status ENUM('upcoming', 'ongoing', 'completed') DEFAULT 'upcoming',
    start_date DATETIME,
    end_date DATETIME,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES Users(user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Teams table
CREATE TABLE Teams (
    team_id INT AUTO_INCREMENT PRIMARY KEY,
    team_name VARCHAR(100) NOT NULL,
    team_code VARCHAR(20),
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Participants table
CREATE TABLE Participants (
    participant_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    team_id INT,
    participant_number VARCHAR(20),
    email VARCHAR(100),
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (team_id) REFERENCES Teams(team_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Judges table
CREATE TABLE Judges (
    judge_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    specialization VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Users(user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Criteria table
CREATE TABLE Criteria (
    criteria_id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    criteria_name VARCHAR(100) NOT NULL,
    description TEXT,
    weight DECIMAL(5,2) NOT NULL DEFAULT 1.00,
    max_score DECIMAL(10,2) NOT NULL DEFAULT 100.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES Events(event_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Schedules table
CREATE TABLE Schedules (
    schedule_id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    schedule_date DATETIME NOT NULL,
    venue VARCHAR(200),
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES Events(event_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- EventParticipants junction table
CREATE TABLE EventParticipants (
    event_participant_id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    participant_id INT,
    team_id INT,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES Events(event_id) ON DELETE CASCADE,
    FOREIGN KEY (participant_id) REFERENCES Participants(participant_id) ON DELETE CASCADE,
    FOREIGN KEY (team_id) REFERENCES Teams(team_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- EventJudges junction table
CREATE TABLE EventJudges (
    event_judge_id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    judge_id INT NOT NULL,
    assigned_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES Events(event_id) ON DELETE CASCADE,
    FOREIGN KEY (judge_id) REFERENCES Judges(judge_id) ON DELETE CASCADE,
    UNIQUE KEY unique_event_judge (event_id, judge_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Scores table
CREATE TABLE Scores (
    score_id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    participant_id INT,
    team_id INT,
    judge_id INT NOT NULL,
    criteria_id INT NOT NULL,
    score_value DECIMAL(10,2) NOT NULL,
    remarks TEXT,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES Events(event_id) ON DELETE CASCADE,
    FOREIGN KEY (participant_id) REFERENCES Participants(participant_id) ON DELETE CASCADE,
    FOREIGN KEY (team_id) REFERENCES Teams(team_id) ON DELETE CASCADE,
    FOREIGN KEY (judge_id) REFERENCES Judges(judge_id),
    FOREIGN KEY (criteria_id) REFERENCES Criteria(criteria_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ScoreDetails table (detailed breakdown)
CREATE TABLE ScoreDetails (
    score_detail_id INT AUTO_INCREMENT PRIMARY KEY,
    score_id INT NOT NULL,
    criteria_id INT NOT NULL,
    score_value DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (score_id) REFERENCES Scores(score_id) ON DELETE CASCADE,
    FOREIGN KEY (criteria_id) REFERENCES Criteria(criteria_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ScoreTotals table (precomputed for performance)
CREATE TABLE ScoreTotals (
    score_total_id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    participant_id INT,
    team_id INT,
    total_score DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    rank_position INT,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES Events(event_id) ON DELETE CASCADE,
    FOREIGN KEY (participant_id) REFERENCES Participants(participant_id) ON DELETE CASCADE,
    FOREIGN KEY (team_id) REFERENCES Teams(team_id) ON DELETE CASCADE,
    UNIQUE KEY unique_event_participant (event_id, participant_id),
    UNIQUE KEY unique_event_team (event_id, team_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Announcements table
CREATE TABLE Announcements (
    announcement_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    event_id INT,
    posted_by INT NOT NULL,
    priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES Events(event_id) ON DELETE CASCADE,
    FOREIGN KEY (posted_by) REFERENCES Users(user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- AuditLogs table
CREATE TABLE AuditLogs (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    table_name VARCHAR(50),
    record_id INT,
    old_value TEXT,
    new_value TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Users(user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default roles
INSERT INTO Roles (role_name, description) VALUES
('Admin', 'System administrator with full access'),
('Event Manager', 'Can create and manage events'),
('USG Officer', 'Can monitor events and generate reports'),
('Judge', 'Can score participants in assigned events'),
('General User', 'Can view public event information');

-- Insert default admin user (password: password - should be hashed)
INSERT INTO Users (email, password, first_name, last_name, role_id) VALUES
('admin@eventrix.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 'User', 1);

-- Create indexes for better performance
CREATE INDEX idx_users_email ON Users(email);
CREATE INDEX idx_events_status ON Events(status);
CREATE INDEX idx_scores_event ON Scores(event_id);
CREATE INDEX idx_scores_participant ON Scores(participant_id);
CREATE INDEX idx_score_totals_event ON ScoreTotals(event_id);
CREATE INDEX idx_announcements_event ON Announcements(event_id);

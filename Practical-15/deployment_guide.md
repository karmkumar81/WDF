# Practical 15: StudentHub Local Deployment Guide

**Course:** Web Development Frameworks (ITUE203)  
**Institution:** Charotar University of Science and Technology (CHARUSAT)  

---

## 1. Prerequisites
- **Web Server:** XAMPP for Windows (Apache 2.4+, PHP 8.1+ / 8.2+)
- **Database:** MySQL 8.0+ or MariaDB 10.4+
- **PHP Extensions Required:** `mysqli`, `pdo_mysql`, `json`, `fileinfo`, `session`

---

## 2. Standard XAMPP Deployment Steps

### Step 1: Database Setup
1. Launch **XAMPP Control Panel** and start **Apache** and **MySQL**.
2. Open your web browser and navigate to `http://localhost/phpmyadmin/`.
3. Click on the **Import** tab.
4. Choose the master SQL file:
   [`Practical-15/studenthub_master.sql`](studenthub_master.sql)
5. Click **Go** to create the `studenthub` database with all 5 tables and initial seed data.

### Step 2: Workspace Directory Placement
Ensure the project folder is accessible under your web root or configured virtual host:
```text
C:\xampp\htdocs\PRACTICALS\
```

### Step 3: Verifying Database Connection
Open `Practical-08/test_connection.php` in your browser:
```text
http://localhost/PRACTICALS/Practical-08/test_connection.php
```
Verify that both PDO and MySQLi report successful connection statuses.

---

## 3. Intermediate Extension: Apache Virtual Host Configuration

To run the StudentHub portal under a custom local domain (e.g., `http://studenthub.local`):

### Step A: Configure Windows Hosts File
1. Open Notepad as **Administrator**.
2. Open file: `C:\Windows\System32\drivers\etc\hosts`.
3. Add the following entry at the bottom:
   ```text
   127.0.0.1    studenthub.local
   ```
4. Save the file.

### Step B: Configure Apache `httpd-vhosts.conf`
1. Open `C:\xampp\apache\conf\extra\httpd-vhosts.conf`.
2. Add the Virtual Host block:
   ```apache
   <VirtualHost *:80>
       ServerAdmin admin@charusat.edu.in
       DocumentRoot "D:/STUDY/WDF/PRACTICALS"
       ServerName studenthub.local
       <Directory "D:/STUDY/WDF/PRACTICALS">
           Options Indexes FollowSymLinks
           AllowOverride All
           Require all granted
       </Directory>
   </VirtualHost>
   ```
3. Restart Apache from the XAMPP Control Panel.
4. Navigate to: `http://studenthub.local/Practical-15/index.php`.

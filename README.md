# 📱 Student Management System - Progressive Web App

A modern, mobile-responsive Student Management System with QR Code attendance tracking, built as a Progressive Web App (PWA). Built with **pure PHP** (no framework), **MySQL**, **HTML5**, **CSS3**, **JavaScript**, and **Bootstrap 5**. Features a clean **black & white color scheme** (10% black, 90% white).

---

## 🌟 Key Highlights

- 📱 **Progressive Web App** - Installable on mobile devices
- 🔄 **Offline Support** - Works without internet connection
- ⚡ **Fast & Responsive** - Cached assets for instant loading
- 📷 **QR Code Scanner** - Camera-based attendance tracking
- 🎨 **Modern UI** - Clean black & white minimalist design
- 🔒 **Secure** - Password hashing, prepared statements, session protection

---

## 📋 Features

### 1. **Authentication System**
- ✅ Admin login & logout
- ✅ Session-based authentication
- ✅ Password hashing using `password_hash()` and `password_verify()`
- ✅ Session timeout protection
- ✅ Auto-reset password feature

### 2. **Student Management (CRUD)**
- ✅ Add new students
- ✅ Edit existing students
- ✅ Delete students
- ✅ View all students with search functionality
- ✅ Auto-generated QR Codes for each student
- ✅ Student fields:
  - Student ID (unique)
  - Full Name
  - Gender (Male/Female/Other)
  - Course
  - Year Level (1st-5th Year)
  - Email (unique)

### 3. **Subject Management**
- ✅ Add, edit, delete subjects
- ✅ Subject Code (unique identifier)
- ✅ Subject Name & Description
- ✅ Student-Subject Enrollment System
- ✅ Track enrolled students per subject

### 4. **Attendance Management**
- ✅ Mark attendance per date and subject
- ✅ Status options: Present, Absent, Late
- ✅ **QR Code Camera Scanner** - Scan using phone camera
- ✅ Manual attendance entry
- ✅ Prevent duplicate attendance
- ✅ View attendance history with filters
- ✅ Comprehensive reports & statistics

### 5. **PWA Features** 🆕
- ✅ **Installable** - Add to home screen
- ✅ **Offline Support** - Basic functionality works offline
- ✅ **App-like Experience** - Standalone mode
- ✅ **Fast Loading** - Service worker caching
- ✅ **Mobile Optimized** - Touch-friendly interface
- ✅ **Offline Indicator** - Shows connection status
- ✅ **Responsive Tables** - Mobile card view

### 6. **Dashboard**
- ✅ Real-time statistics
- ✅ Recent attendance records
- ✅ Quick action buttons
- ✅ PWA install prompt
- ✅ Quick action buttons

### 6. **QR Code Integration**
- ✅ Auto-generate QR codes for each student using their Student ID
- ✅ Display QR codes in student list
- ✅ Download QR codes as PNG images
- ✅ Quick attendance marking via QR code scan
- ✅ External API: `api.qrserver.com` (free, no API key required)

### 7. **Reports & Statistics**
- ✅ Attendance rate by student
- ✅ Attendance rate by subject
- ✅ Daily attendance trends
- ✅ Date range filtering
- ✅ Print-friendly reports

---

## 🎨 Design Features

### Color Palette
- **Primary Black:** `#000000` (10% usage)
- **Light Gray:** `#f5f5f5` (90% usage)
- **White:** `#ffffff`
- Minimalist, professional black & white theme

### UI Components
- Responsive Bootstrap 5 layout
- Clean card-based design
- Hover effects on buttons and tables
- Status badges (Present=Green, Absent=Red, Late=Yellow)
- Custom scrollbar styling
- Mobile-friendly responsive design

---

## 🗂️ Folder Structure

```
student-management-system/
│
├── config/
│   └── database.php          # PDO database connection
│
├── auth/
│   ├── login.php             # Login page
│   ├── login_process.php     # Login handler
│   └── logout.php            # Logout handler
│
├── students/
│   ├── index.php             # List students
│   ├── add.php               # Add student
│   ├── edit.php              # Edit student
│   └── delete.php            # Delete student
│
├── subjects/
│   ├── index.php             # List subjects
│   ├── add.php               # Add subject
│   ├── edit.php              # Edit subject
│   └── delete.php            # Delete subject
│
├── attendance/
│   ├── mark.php              # Mark attendance (QR + Manual)
│   ├── view.php              # View attendance records
│   └── report.php            # Attendance reports & statistics
│
├── assets/
│   ├── css/
│   │   └── style.css         # Custom CSS
│   ├── js/
│   │   └── script.js         # Custom JavaScript
│   └── images/               # Image assets
│
├── includes/
│   ├── header.php            # Header & navigation
│   ├── footer.php            # Footer
│   └── auth_check.php        # Session validation
│
├── index.php                 # Dashboard
├── database.sql              # Database schema
└── README.md                 # This file
```

---

## 🚀 Installation & Setup

### Prerequisites
- **XAMPP** (or any PHP 7.4+ with MySQL)
- Modern web browser (Chrome, Edge, Safari, or Firefox)

### Step 1: Database Setup
1. Open **phpMyAdmin** (`http://localhost/phpmyadmin`)
2. Create a new database named `student_management_system2026`
3. Import the `database.sql` file:
   - Click on the database
   - Go to **Import** tab
   - Choose `database.sql` file
   - Click **Go**

### Step 2: Configure Database Connection
1. Open `config/database.php`
2. Update database credentials if needed:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'student_management_system2026');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   ```

### Step 3: Start XAMPP
1. Start **Apache** and **MySQL** services
2. Place the project folder in `C:\xampp\htdocs\amsp`

### Step 4: Access the System
1. Open browser and go to: `http://localhost/amsp/`
2. You'll be redirected to the login page

---

## 🔐 Default Login Credentials

**Username:** `Von` or `admin`  
**Password:** `admin123`

> ⚠️ **Note:** The system has an auto-reset password feature that will update the hash on first login if needed.

---

## 📱 Installing as Mobile App (PWA)

### On Android (Chrome)
1. Open `http://your-server-ip/amsp/` in Chrome
2. Look for the install banner at the top of the dashboard
3. Click "Install App" button
4. Or tap menu (⋮) → "Install app"
5. The app will appear on your home screen with the SMS icon

### On iOS (Safari)
1. Open `http://your-server-ip/amsp/` in Safari
2. Tap the Share button (⬆️) at the bottom
3. Scroll down and tap "Add to Home Screen"
4. Enter "Student Management" as the name
5. Tap "Add" - the app will appear on your home screen

### On Desktop (Chrome/Edge)
1. Open the website in Chrome or Edge
2. Look for the install icon (➕) in the address bar
3. Click "Install" when prompted
4. The app opens in its own window

### Testing PWA on Mobile Devices (Local Network)
1. **Find your computer's IP address**:
   - Windows: Open Command Prompt, type `ipconfig`
   - Look for "IPv4 Address" (e.g., 192.168.1.xxx)

2. **Ensure mobile is on same WiFi network**

3. **Access from mobile browser**:
   ```
   http://YOUR_IP_ADDRESS/amsp/
   ```
   Example: `http://192.168.1.100/amsp/`

4. **Follow installation steps above** for your mobile OS

---

## 📊 Database Schema

### Tables

#### 1. `users` - Admin authentication
```sql
- id (INT, PRIMARY KEY)
- username (VARCHAR, UNIQUE)
- password (VARCHAR, hashed)
- full_name (VARCHAR)
- email (VARCHAR, UNIQUE)
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)
```

#### 2. `students` - Student records
```sql
- id (INT, PRIMARY KEY)
- student_id (VARCHAR, UNIQUE)
- full_name (VARCHAR)
- gender (ENUM: Male, Female, Other)
- course (VARCHAR)
- year_level (ENUM: 1st-5th Year)
- email (VARCHAR, UNIQUE)
- qr_code (VARCHAR)
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)
```

#### 3. `subjects` - Subject management
```sql
- id (INT, PRIMARY KEY)
- subject_code (VARCHAR, UNIQUE)
- subject_name (VARCHAR)
- description (TEXT)
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)
```

#### 4. `student_subjects` - Student-subject enrollment
```sql
- id (INT, PRIMARY KEY)
- student_id (INT, FOREIGN KEY)
- subject_id (INT, FOREIGN KEY)
- enrolled_at (TIMESTAMP)
```

#### 5. `attendance` - Attendance records
```sql
- id (INT, PRIMARY KEY)
- student_id (INT, FOREIGN KEY)
- subject_id (INT, FOREIGN KEY)
- attendance_date (DATE)
- status (ENUM: Present, Absent, Late)
- remarks (TEXT)
- marked_by (INT, FOREIGN KEY to users)
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)
- UNIQUE KEY: (student_id, subject_id, attendance_date)
```

---

## 🔧 Key Technologies & Libraries

### Backend
- **PHP 7.4+** (Pure PHP, no framework)
- **PDO** (MySQL with prepared statements)
- **Session Management**
- **Password Hashing** (`password_hash()`, `password_verify()`)

### Frontend
- **HTML5**
- **CSS3** (Custom black & white theme)
- **JavaScript (ES6+)**
- **Bootstrap 5.3.0** (CDN)
- **Bootstrap Icons 1.11.1** (CDN)

### External APIs
- **QR Code API:** `https://api.qrserver.com/v1/create-qr-code/`
  - Free, no API key required
  - Generates QR codes on-the-fly
  - Used for student identification

---

## 📱 Features Breakdown

### 1. QR Code Attendance System

#### How it works:
1. **Student Registration:** Each student gets a unique QR code generated using their Student ID
2. **QR Display:** View QR codes from the student list
3. **Download:** Download individual QR codes as PNG images
4. **Quick Scan:** On the "Mark Attendance" page, scan the QR code or manually enter Student ID
5. **Instant Marking:** Student is immediately marked as "Present" for the selected subject and date

#### Benefits:
- ✅ Fast attendance marking (5-10 seconds per student)
- ✅ Reduces manual errors
- ✅ Contactless attendance
- ✅ Real-time tracking

### 2. Security Features
- ✅ Session-based authentication
- ✅ Password hashing (bcrypt algorithm)
- ✅ SQL injection prevention (prepared statements)
- ✅ XSS protection (htmlspecialchars)
- ✅ CSRF protection (session validation)
- ✅ Auto-logout on session timeout

### 3. User Experience
- ✅ Clean, minimalist design
- ✅ Fast page loads
- ✅ Responsive mobile layout
- ✅ Real-time form validation
- ✅ Success/error notifications
- ✅ Confirm dialogs for delete actions
- ✅ Search and filter functionality

---

## 🎯 Usage Guide

### Adding a Student
1. Go to **Students** → **Add New Student**
2. Fill in student details
3. Click **Save Student**
4. QR code is auto-generated

### Marking Attendance (QR Method)
1. Go to **Attendance** → **Mark Attendance**
2. Select **Subject** and **Date**
3. Use QR scanner or type Student ID
4. Click **Mark Present**

### Marking Attendance (Manual Method)
1. Go to **Attendance** → **Mark Attendance**
2. Scroll to **Manual Attendance Entry**
3. Select **Subject** and **Date**
4. Mark each student as Present/Absent/Late
5. Click **Save Attendance**

### Viewing Reports
1. Go to **Attendance** → **Reports**
2. Select date range
3. View statistics by student, subject, or daily trend
4. Use **Print** button for physical reports

---

## 🛡️ Security Best Practices

1. **Change Default Password:** Update admin password immediately
2. **Use HTTPS:** Deploy with SSL certificate in production
3. **Regular Backups:** Backup database regularly
4. **Update PHP:** Keep PHP and MySQL updated
5. **File Permissions:** Set proper file permissions (755 for directories, 644 for files)

---

## 🐛 Troubleshooting

### Issue: "Database connection failed"
**Solution:** Check `config/database.php` credentials and ensure MySQL is running

### Issue: "Session not working"
**Solution:** Ensure `session_start()` is called and PHP session extension is enabled

### Issue: "QR codes not loading"
**Solution:** Check internet connection (external API required) or update QR API endpoint

### Issue: "Blank page after login"
**Solution:** Enable PHP error reporting: `error_reporting(E_ALL);`

---

## 📝 Code Quality Standards

### This project follows:
- ✅ PSR-12 coding standards
- ✅ DRY principle (Don't Repeat Yourself)
- ✅ Separation of concerns
- ✅ Prepared statements for all database queries
- ✅ Input validation and sanitization
- ✅ Well-commented code
- ✅ Consistent naming conventions

---

## 🚀 Future Enhancements (Optional)

- [ ] Student photo upload
- [ ] Email notifications
- [ ] SMS integration for absent students
- [ ] Bulk student import (CSV/Excel)
- [ ] Multi-user roles (Teacher, Admin)
- [ ] Mobile app (PWA)
- [ ] Biometric attendance
- [ ] Parent portal
- [ ] Grade management
- [ ] Course scheduling

---

## 📄 License

This project is open-source and available for educational purposes.

---

## 👨‍💻 Developer Notes

### Important Files:
- **Authentication Logic:** `auth/login_process.php`
- **Database Helpers:** `config/database.php`
- **Session Check:** `includes/auth_check.php`
- **Main Layout:** `includes/header.php`, `includes/footer.php`

### CSS Customization:
All custom styles are in `assets/css/style.css`. Modify color variables:
```css
:root {
    --primary-black: #000000;
    --light-gray: #f5f5f5;
    --white: #ffffff;
}
```

### Adding New Features:
1. Create new PHP file in appropriate folder
2. Include `auth_check.php` for protected pages
3. Include `header.php` and `footer.php` for layout
4. Use `query()`, `fetchAll()`, `fetchOne()` helper functions

---

## 📞 Support

For issues or questions:
1. Check the **Troubleshooting** section
2. Review the code comments
3. Ensure all prerequisites are met

---

## ✅ System Testing Checklist

- [x] Login/Logout functionality
- [x] Add/Edit/Delete students
- [x] Add/Edit/Delete subjects
- [x] Mark attendance (manual)
- [x] Mark attendance (QR code)
- [x] View attendance records
- [x] Generate reports
- [x] Search and filter features
- [x] Responsive design
- [x] Session timeout
- [x] QR code generation
- [x] Form validation

---

**🎓 Student Management System v1.0**  
**Built with ❤️ using Pure PHP & MySQL**  
**Color Theme: 10% Black, 90% White**  
**QR Code Powered Attendance Tracking**

---

**Happy Coding! 🚀**
#   A t t e n d a n c e - W e b - A p p  
 #   A t t e n d a n c e - A p p  
 
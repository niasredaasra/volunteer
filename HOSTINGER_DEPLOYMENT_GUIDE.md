# 🚀 Hostinger Deployment Guide - Volunteer App

## 📋 **Pre-Deployment Checklist**

### **Files to Upload:**
✅ Upload these folders/files:
- `api/` (all PHP files)
- `admin/` (admin interface)
- `index.php` (main form)
- `config.php` (database configuration)

### **Files NOT to Upload:**
❌ Don't upload these:
- `test_whatsapp.php`
- `fix_mobile_numbers.php`
- `test_with_verified_number.php`
- `WHATSAPP_DEBUG_GUIDE.md`
- `HOSTINGER_DEPLOYMENT_GUIDE.md`

## 🗂️ **Step 1: File Upload**

### **Method A: Using Hostinger File Manager**
1. Login to **Hostinger Control Panel**
2. Go to **File Manager**
3. Navigate to **public_html**
4. Create folder: **volunteer_app**
5. Upload all required files to this folder

### **Method B: Using FTP/cPanel**
1. Use FTP client (FileZilla)
2. Connect with Hostinger FTP details
3. Upload files to `/public_html/volunteer_app/`

## 🗄️ **Step 2: Database Setup**

### **Create Database:**
1. Hostinger Control Panel → **Databases**
2. Click **"Create Database"**
3. Database Name: `u123456_shelter` (Hostinger format)
4. Username: `u123456_shelter`
5. Password: Create strong password
6. Note down these details!

### **Import Database Structure:**
Run these SQL commands in **phpMyAdmin**:

```sql
-- Create Villages Table
CREATE TABLE villages (
    id int(11) NOT NULL AUTO_INCREMENT,
    village_name varchar(100) NOT NULL,
    PRIMARY KEY (id)
);

INSERT INTO villages (village_name) VALUES 
('Mustafabad'), ('Saran'), ('Delhi'), ('Gurgaon'), ('Other');

-- Create Occupations Table
CREATE TABLE occupations (
    id int(11) NOT NULL AUTO_INCREMENT,
    occupation_name varchar(100) NOT NULL,
    PRIMARY KEY (id)
);

INSERT INTO occupations (occupation_name) VALUES 
('farmer'), ('teacher'), ('doctor'), ('engineer'), ('other');

-- Create Seva Interests Table
CREATE TABLE seva_interests (
    id int(11) NOT NULL AUTO_INCREMENT,
    seva_name varchar(100) NOT NULL,
    PRIMARY KEY (id)
);

INSERT INTO seva_interests (seva_name) VALUES 
('Help'), ('Teaching'), ('Medical'), ('Food Distribution'), ('Other');

-- Create Items Table
CREATE TABLE items (
    id int(11) NOT NULL AUTO_INCREMENT,
    item_name varchar(100) NOT NULL,
    PRIMARY KEY (id)
);

INSERT INTO items (item_name) VALUES 
('Clothes'), ('Blanket'), ('Shoes'), ('Utensils'), ('Bag'), ('Other');

-- Create Cities Table
CREATE TABLE cities (
    id int(11) NOT NULL AUTO_INCREMENT,
    city_name varchar(100) NOT NULL,
    PRIMARY KEY (id)
);

INSERT INTO cities (city_name) VALUES 
('Delhi'), ('Mumbai'), ('Gurgaon'), ('Noida'), ('Other');

-- Create States Table
CREATE TABLE states (
    id int(11) NOT NULL AUTO_INCREMENT,
    state_name varchar(100) NOT NULL,
    PRIMARY KEY (id)
);

INSERT INTO states (state_name) VALUES 
('Delhi'), ('Haryana'), ('Uttar Pradesh'), ('Maharashtra'), ('Other');

-- Create Countries Table
CREATE TABLE countries (
    id int(11) NOT NULL AUTO_INCREMENT,
    country_name varchar(100) NOT NULL,
    PRIMARY KEY (id)
);

INSERT INTO countries (country_name) VALUES 
('India'), ('USA'), ('Canada'), ('UK'), ('Other');

-- Create Volunteers Table
CREATE TABLE volunteers (
    id int(11) NOT NULL AUTO_INCREMENT,
    name varchar(255) NOT NULL,
    mobile varchar(15) DEFAULT NULL,
    email varchar(255) DEFAULT NULL,
    village_id int(11) DEFAULT NULL,
    city_id int(11) DEFAULT NULL,
    state_id int(11) DEFAULT NULL,
    country_id int(11) DEFAULT NULL,
    occupation_id int(11) DEFAULT NULL,
    seva_interest_id int(11) DEFAULT NULL,
    items_brought text DEFAULT NULL,
    created_at timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (id),
    KEY fk_volunteers_village (village_id),
    KEY fk_volunteers_occupation (occupation_id),
    KEY fk_volunteers_seva (seva_interest_id),
    KEY fk_volunteers_city (city_id),
    KEY fk_volunteers_state (state_id),
    KEY fk_volunteers_country (country_id),
    CONSTRAINT fk_volunteers_city FOREIGN KEY (city_id) REFERENCES cities (id),
    CONSTRAINT fk_volunteers_country FOREIGN KEY (country_id) REFERENCES countries (id),
    CONSTRAINT fk_volunteers_occupation FOREIGN KEY (occupation_id) REFERENCES occupations (id),
    CONSTRAINT fk_volunteers_seva FOREIGN KEY (seva_interest_id) REFERENCES seva_interests (id),
    CONSTRAINT fk_volunteers_state FOREIGN KEY (state_id) REFERENCES states (id),
    CONSTRAINT fk_volunteers_village FOREIGN KEY (village_id) REFERENCES villages (id)
);
```

## ⚙️ **Step 3: Update Configuration**

### **Edit config.php for Live Server:**
```php
<?php
// === Database (Hostinger) ===
define('DB_HOST', 'localhost');
define('DB_USER', 'u123456_shelter');     // Your DB username
define('DB_PASS', 'your_db_password');    // Your DB password
define('DB_NAME', 'u123456_shelter');     // Your DB name

// === WhatsApp Cloud API ===
define('WA_PHONE_NUMBER_ID', '793669113818748');
define('WA_TOKEN', 'your_access_token_here');
define('WA_API_VERSION', 'v20.0');
?>
```

## 🔗 **Step 4: Access URLs**

After deployment, your app will be accessible at:
- **Main Form:** `https://yourdomain.com/volunteer_app/`
- **Admin Panel:** `https://yourdomain.com/volunteer_app/admin/list.php`

## ✅ **Step 5: Testing**

### **Test Database Connection:**
Create `test_connection.php`:
```php
<?php
require_once 'api/db.php';
try {
    $conn = db();
    echo "✅ Database connected successfully!";
    echo "<br>Server: " . $conn->host_info;
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage();
}
?>
```

### **Test Form Submission:**
1. Fill volunteer form
2. Submit
3. Check admin panel for new entry

### **Test WhatsApp (after adding test numbers):**
1. Add your number to Meta Dashboard test list
2. Send test message from admin panel
3. Check WhatsApp delivery

## 🚨 **Common Issues & Solutions**

### **Database Connection Error:**
- Check database credentials in `config.php`
- Ensure database exists in Hostinger panel
- Verify username/password

### **File Upload Issues:**
- Check file permissions (755 for folders, 644 for files)
- Ensure proper folder structure
- Clear browser cache

### **WhatsApp Not Working:**
- Add recipient numbers to Meta test list
- Verify Phone Number ID and Token
- Check error logs in Hostinger

## 📞 **Support Contacts**
- **Hostinger Support:** Live chat in control panel
- **Meta WhatsApp Support:** developers.facebook.com
- **PHP Errors:** Check Hostinger error logs

## 🎯 **Quick Deployment Steps**
1. Upload files via File Manager
2. Create database via Hostinger panel
3. Import SQL structure
4. Update config.php
5. Test form submission
6. Add numbers to WhatsApp test list
7. Test messaging

Your volunteer app will be live and working! 🚀



# Visitor Management System

This is an extension to the existing volunteer registration system that adds comprehensive visitor management functionality with visit history tracking.

## 🎯 Features Implemented

### ✅ Core Requirements

1. **Mobile Number First Step**
   - Mobile number input as the very first field
   - Real-time validation on `onBlur` event
   - API call to check if mobile exists in database

2. **New Visitor Flow**
   - If mobile number is new → show full registration form
   - All fields enabled for data entry
   - Single submission creates visitor + initial visit record

3. **Existing Visitor Flow**
   - If mobile number exists → show popup modal
   - Pre-filled visitor info (hidden by default)
   - Visit history display (hidden by default) 
   - "Show Details" button to toggle visibility

4. **Visit History Tracking**
   - Table format with Date-Time, Items Brought, Remarks
   - JSON storage for flexible item data
   - Automatic timestamp for each visit

5. **New Visit Entry**
   - Multi-select dropdown for items brought
   - Textarea for remarks
   - Current datetime auto-populated on save

### ✅ Database Structure

- **visitors table**: Main visitor information (mobile as unique identifier)
- **visitor_visits table**: Visit history records linked to visitors
- **Foreign key relationships**: Proper referential integrity
- **JSON storage**: Flexible items_brought field

### ✅ UI/UX Features

- **React + TailwindCSS**: Modern, responsive interface
- **Modal popup**: Clean overlay for existing visitors
- **Collapsible sections**: Show/hide details and history
- **Real-time feedback**: Loading states and validation
- **Mobile-first design**: Works well on all devices

## 📁 Files Created/Modified

### New Files
1. **`visitor_management.html`** - Main React interface
2. **`api/visitors.php`** - Backend API endpoints
3. **`create_visitor_management_schema.sql`** - Database schema
4. **`setup_visitor_management.php`** - Setup script
5. **`VISITOR_MANAGEMENT_README.md`** - This documentation

### Database Tables
- `visitors` - Main visitor records
- `visitor_visits` - Visit history

## 🚀 Quick Setup

### Step 1: Create Database Tables
Run the setup script in your browser:
```
http://localhost/volunteer_app/setup_visitor_management.php
```

Or manually execute the SQL:
```sql
-- Run create_visitor_management_schema.sql in phpMyAdmin
```

### Step 2: Access the System
Open the visitor management interface:
```
http://localhost/volunteer_app/visitor_management.html
```

## 📡 API Endpoints

### 1. Check Mobile Number
```
GET api/visitors.php?fn=check-mobile&mobile=9876543210
Response: {"exists": true/false}
```

### 2. Add New Visitor
```
POST api/visitors.php?fn=add-visitor
Body: FormData with visitor details
Response: {"ok": true, "visitor_id": 123, "visit_id": 456}
```

### 3. Add Visit for Existing Visitor
```
POST api/visitors.php?fn=add-visit
Body: mobile, items_brought[], remarks
Response: {"ok": true, "visit_id": 789}
```

### 4. Get Visitor History
```
GET api/visitors.php?fn=get-history&mobile=9876543210
Response: {
  "ok": true, 
  "visitor": {
    "name": "John Doe",
    "mobile": "9876543210",
    "visits": [...]
  }
}
```

### 5. List All Visitors
```
GET api/visitors.php?fn=list
Response: [{"id": 1, "name": "...", "total_visits": 3}, ...]
```

## 🔧 Technical Implementation

### Frontend (React + TailwindCSS)
- **Mobile-first validation**: `onBlur` event triggers API call
- **Conditional rendering**: Show form OR modal based on mobile check
- **State management**: React hooks for form state and UI state
- **API integration**: Axios for HTTP requests
- **Responsive design**: TailwindCSS for styling

### Backend (PHP + MySQL)
- **RESTful API**: Clean endpoint structure with `fn` parameter
- **Database transactions**: Ensure data integrity
- **Error handling**: Comprehensive error responses
- **JSON support**: Modern data storage for flexible fields

### Database Design
```sql
visitors (
  id, name, mobile*, email, phone, village_id, 
  city_id, state_id, country_id, occupation_id, 
  seva_interest_id, dob, created_at, updated_at
)

visitor_visits (
  id, visitor_id, items_brought (JSON), 
  remarks, visit_date
)
```

## 🎛️ Usage Workflow

1. **User enters mobile number** → onBlur triggers validation
2. **If new number**:
   - Form unlocks with all fields
   - User fills complete information
   - Submit creates visitor + first visit
3. **If existing number**:
   - Modal opens immediately
   - "New Visit Entry" section always visible
   - "Show Details" button reveals visitor info + history
   - User can record new visit quickly

## 🔮 Future Enhancements Ready

### Bulk SMS/WhatsApp Integration
- Database ensures one record per unique mobile number
- `visitors.mobile` is unique constraint
- Visit history won't create duplicate contacts

### Reporting & Analytics
- Visit frequency analysis
- Popular items tracking
- Visitor demographics
- Activity timelines

### Advanced Features
- QR code generation for quick check-in
- Photo capture for visits
- Visitor check-in/check-out times
- Multi-location support

## 🛠️ Troubleshooting

### Common Issues

1. **"Mobile number already exists" error**
   - Mobile numbers must be unique in visitors table
   - Use existing visitor flow for returning visitors

2. **API endpoints not working**
   - Ensure `api/visitors.php` has proper permissions
   - Check database connection in `config.php`
   - Verify all required tables exist

3. **Dropdown data not loading**
   - Ensure existing master tables have data
   - Check API endpoints: villages.php, cities.php, etc.
   - Verify CORS settings if needed

### Database Verification
```sql
-- Check if tables exist
SHOW TABLES LIKE 'visitors';
SHOW TABLES LIKE 'visitor_visits';

-- Check sample data
SELECT COUNT(*) FROM visitors;
SELECT COUNT(*) FROM visitor_visits;

-- Test API functionality
SELECT mobile, COUNT(*) as visits 
FROM visitors v 
LEFT JOIN visitor_visits vv ON v.id = vv.visitor_id 
GROUP BY v.id;
```

## 📊 Database Migration

If you have existing volunteer data and want to migrate:

```sql
-- Option 1: Copy volunteers to visitors
INSERT INTO visitors (name, mobile, email, phone, village_id, city_id, state_id, country_id, occupation_id, seva_interest_id, dob, created_at)
SELECT name, mobile, email, phone, village_id, city_id, state_id, country_id, occupation_id, seva_interest_id, dob, created_at
FROM volunteers 
WHERE mobile IS NOT NULL AND mobile != '';

-- Option 2: Create initial visit records from volunteers
INSERT INTO visitor_visits (visitor_id, items_brought, remarks, visit_date)
SELECT v.id, vol.items_brought, vol.remarks, vol.created_at
FROM visitors v
JOIN volunteers vol ON v.mobile = vol.mobile;
```

## 🔗 Integration with Existing System

- **Preserves existing volunteer registration** at `index.php`
- **Independent database tables** - no conflicts
- **Shared master data** - uses same villages, cities, etc.
- **Same API patterns** - consistent with existing code style

## 📝 Notes

- Mobile number serves as the unique identifier
- Visit history is stored as JSON for flexibility
- System supports multiple visits per visitor
- Real-time validation provides immediate feedback
- Responsive design works on mobile and desktop

---

## 🎉 Ready to Use!

The visitor management system is now fully functional and ready for production use. All requirements have been implemented with modern React frontend and robust PHP/MySQL backend.



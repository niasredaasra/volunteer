Volunteer App (PHP + MySQL for XAMPP)
=====================================
What this includes:
- index.php: Volunteer Registration Form
- api/: PHP APIs for occupations, seva interests, suggestions, save volunteer, and WhatsApp send
- admin/list.php: Simple list view + WhatsApp broadcast UI
- config.php: App configuration (DB + WhatsApp API). Edit this file before use.

Prerequisites:
- XAMPP installed and running (Apache + MySQL)
- MySQL database "shelter" with tables you already created:
    volunteers, occupations, seva_interests
  (from your earlier SQL)
- PHP extensions enabled (mysqli, curl)

Setup:
1) Copy the 'volunteer_app' folder into XAMPP's htdocs, e.g.:
   C:\xampp\htdocs\volunteer_app  (Windows)
   /Applications/XAMPP/htdocs/volunteer_app (macOS)
   /opt/lampp/htdocs/volunteer_app (Linux)

2) Open config.php and set your DB credentials if needed (default root / no password / shelter).
   Also set WhatsApp Cloud API details:
   - WA_PHONE_NUMBER_ID
   - WA_TOKEN
   - WA_API_VERSION (e.g., v20.0; change if needed)

3) Visit in browser:
   http://localhost/volunteer_app/index.php       (form)
   http://localhost/volunteer_app/admin/list.php  (admin + broadcast)

Notes:
- Village/City/State/Country fields use <datalist> suggestions from previously entered volunteers.
  You can type a new value; once saved, it will appear as a suggestion for future entries.
- "Occupation" and "Interested Seva" are managed via master tables with "Add" buttons.
- WhatsApp Broadcast:
  * This sends messages one-by-one to selected volunteers via Meta's WhatsApp Cloud API.
  * Marketing/broadcast messaging requires template approval and user opt-in as per WhatsApp policy.
  * Put a public video URL in the "Video URL" box if you want to send a video. The API fetches the video from the link.
  * For testing, add your phone number as a test recipient in the WhatsApp Business app settings.

Security Basics:
- Prepared statements are used.
- Minimal input validation is included. For production, add stricter validation and CSRF protection.
- Restrict access to admin pages and keep your WA token secret.

# DEPLOYMENT INSTRUCTIONS

## STEP 1: ALTER YOUR DATABASE
Run this SQL in your Hostinger database:

```sql
ALTER TABLE form_submissions ADD COLUMN form_data JSON NULL;
```

This adds ONE column to store all form fields as JSON.

---

## STEP 2: DELETE OLD FILES
Delete these from FTP public_html/:
- submit_form.php (OLD VERSION)

---

## STEP 3: UPLOAD NEW FILES
Upload these files to FTP public_html/:

1. **index.html** - Admin login page
2. **admin-dashboard.html** - Admin portal
3. **customer.html** - Customer information form (3 steps)
4. **property.html** - Property information form (4 steps)
5. **submit_form.php** - ⭐ RENAMED from submit_form_CORRECTED.php
6. **admin_login.php** - Login handler
7. **check_session.php** - Session checker
8. **admin_api.php** - API for dashboard
9. **create_invitation.php** - Invitation creator
10. **config.php** - Database config
11. **baldwin-logo.png** - Logo image

---

## STEP 4: TEST IT
1. Go to https://baldwin.claysites.com/
2. Login with: admin / changeme123
3. Click "Create Invitation"
4. Fill: Name, Email, Form Type (Customer or Property)
5. Check your email for invitation link
6. Click link and fill out form
7. Submit form
8. Go back to admin dashboard
9. Click "Refresh"
10. **YOUR SUBMISSION SHOULD APPEAR** ✓

---

## HOW IT WORKS NOW

**Before (BROKEN):**
- Forms collect 40+ fields
- Database only saves 13 fields
- Data gets lost ❌

**After (FIXED):**
- Forms collect 40+ fields
- ALL data saved as JSON in form_data column
- Dashboard can display it all ✓
- Data never lost ✓

---

## FILES PROVIDED

✓ index.html - READY
✓ admin-dashboard.html - READY
✓ customer.html - READY (3 steps)
✓ property.html - READY (4 steps)
✓ submit_form.php - READY (CORRECTED VERSION)
✓ admin_login.php - UNCHANGED
✓ check_session.php - UNCHANGED
✓ admin_api.php - UNCHANGED
✓ create_invitation.php - UNCHANGED
✓ config.php - UNCHANGED
✓ baldwin-logo.png - UNCHANGED
✓ ALTER_DATABASE.sql - RUN FIRST


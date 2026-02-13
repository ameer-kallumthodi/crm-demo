# Online Teaching Faculty Public Form System

## Overview
This system allows each faculty member in the list to have a unique public form link that they can use to fill in their details. The form requires all fields to be filled and documents to be uploaded.

## Features Implemented

### 1. Database Changes
- Added `form_token` column: Stores a unique token for each faculty member (for future use)
- Added `form_filled_at` column: Tracks when the form was submitted

### 2. Public Form Link Generation
- Each faculty in the admin list has a "Copy Form Link" button (green copy icon)
- Clicking the button generates a simple URL using the faculty ID
- The link format: `https://crm-demo.test/faculty-form/{id}` (e.g., `https://crm-demo.test/faculty-form/1`)
- Also includes an "Open Form Link" button to preview the form in a new tab

### 3. Public Form Features
- **All fields are required** (except alternate contact and optional documents)
- Same fields as the admin add form
- Beautiful gradient design with purple theme
- Responsive layout
- File upload validation (max 10MB per file)
- Form can only be submitted once per token

### 4. Form States
- **Empty Form**: Faculty can fill and submit
- **Already Submitted**: Shows a message that form was already submitted with submission date
- **Success Page**: Displays after successful submission with timestamp

### 5. Required Fields in Public Form
**Personal Details:**
- Full Name *
- Date of Birth *
- Gender *
- Primary Mobile Number *
- Official Email Address *
- Father's Name *
- Mother's Name *

**Address:**
- House Name / Flat No. *
- Area / Locality *
- Village / Town / City *
- District *
- State *
- PIN Code *

**Education & Experience:**
- Highest Educational Qualification *
- Teaching Experience *
- Department Name *

**Documents (All Required):**
- Updated Resume / CV *
- 10th Certificate *
- Educational Qualification Certificates *
- Aadhaar Card (Front Side) *
- Aadhaar Card (Back Side) *
- Other Supporting Document – 1 (Optional)
- Other Supporting Document – 2 (Optional)

## How to Use

### For Admin:
1. Go to `https://crm-demo.test/admin/online-teaching-faculties`
2. Find the faculty member in the list
3. Click the green "Copy Form Link" button (link icon)
4. Share the copied link with the faculty member

### For Faculty:
1. Open the unique link provided by admin
2. Fill all required fields
3. Upload all required documents
4. Submit the form
5. See success confirmation

## Technical Details

### Routes
- Public Form: `GET /faculty-form/{id}`
- Public Submit: `POST /faculty-form/{id}`
- Generate Link: `GET /admin/online-teaching-faculties/{id}/generate-form-link`

### Files Created/Modified
1. **Migration**: `2026_02_13_100831_add_form_token_to_online_teaching_faculties_table.php`
2. **Controller Methods**: 
   - `generateFormToken()`
   - `publicForm()`
   - `publicSubmit()`
3. **Views**:
   - `resources/views/public/faculty-form.blade.php`
   - `resources/views/public/faculty-form-success.blade.php`
   - `resources/views/public/faculty-form-already-filled.blade.php`
4. **JavaScript**: Updated `public/assets/js/online-teaching-faculties.js`
5. **Actions Partial**: Updated to include copy link button

### Security Features
- Simple ID-based URLs for easy sharing
- Form can only be submitted once per faculty
- CSRF protection
- File upload validation

## Notes
- The form link uses the faculty ID (e.g., `/faculty-form/1`)
- Links are simple and easy to share
- Once submitted, the form cannot be resubmitted
- All uploaded documents are stored in `storage/app/public/online-teaching-faculties/{faculty_id}/`
- The system tracks submission time in `form_filled_at` field
- Pre-filled data allows faculty to review and update their information
- Documents already uploaded are optional (can be replaced if needed)

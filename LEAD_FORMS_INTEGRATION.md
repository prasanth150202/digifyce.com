# Lead Forms Backend Integration - Implementation Guide

## Overview
Successfully connected 3 lead forms to the backend database with AJAX submissions:
1. **PDF Email Lead Form** (Strategy Matrix section on homepage)
2. **Main Lead Form** (leadform.php)
3. **Spontaneous Job Application Form** (careers.php)

## Database Tables Created

### 1. `pdf_email_leads`
Stores email addresses from users requesting PDF downloads.

**Fields:**
- `id` - Auto-increment primary key
- `email` - User's email address (255 chars)
- `source` - Source of lead (default: 'strategy_matrix')
- `ip_address` - User's IP address
- `user_agent` - Browser user agent
- `created_at` - Timestamp of submission

### 2. `lead_form_submissions`
Stores main lead form submissions from leadform.php.

**Fields:**
- `id` - Auto-increment primary key
- `full_name` - Contact's full name (128 chars)
- `email` - Contact's email address (255 chars)
- `phone` - Contact's phone number (32 chars, optional)
- `company` - Company name (128 chars, optional)
- `budget` - Selected budget range (32 chars, optional)
- `website` - Company website URL (255 chars, optional)
- `message` - Project details/message (TEXT, required)
- `ip_address` - User's IP address
- `user_agent` - Browser user agent
- `created_at` - Timestamp of submission

### 3. `job_applications`
Stores job applications with CV upload support.

**Fields:**
- `id` - Auto-increment primary key
- `full_name` - Applicant's full name (128 chars)
- `email` - Applicant's email (255 chars)
- `portfolio_url` - LinkedIn/Portfolio URL (255 chars)
- `cover_letter` - Why they're a fit (TEXT)
- `cv_filename` - Uploaded CV filename (255 chars, optional)
- `cv_filepath` - Path to uploaded CV (512 chars, optional)
- `job_opening_id` - Foreign key to job_openings table (optional)
- `ip_address` - User's IP address
- `user_agent` - Browser user agent
- `status` - Application status (pending/reviewed/shortlisted/rejected)
- `created_at` - Timestamp of submission

## API Endpoints

### 1. `/app/api/pdf_email_lead.php`
**Method:** POST  
**Content-Type:** application/json

**Request Body:**
```json
{
  "email": "user@example.com",
  "source": "strategy_matrix" // optional
}
```

**Response:**
```json
{
  "success": true,
  "message": "Thank you! Check your email for the PDF.",
  "id": 123
}
```

### 2. `/app/api/lead_form_submit.php`
**Method:** POST  
**Content-Type:** application/json

**Request Body:**
```json
{
  "full_name": "John Smith",
  "email": "john@example.com",
  "phone": "+1 555-0000",
  "company": "Acme Corp",
  "budget": "10k-25k",
  "website": "https://example.com",
  "message": "I need help with..."
}
```

**Response:**
```json
{
  "success": true,
  "message": "Thank you! Your details were submitted successfully...",
  "id": 456
}
```

### 3. `/app/api/job_application_submit.php`
**Method:** POST  
**Content-Type:** multipart/form-data

**Form Fields:**
- `full_name` (required)
- `email` (required)
- `portfolio_url` (required)
- `cover_letter` (required)
- `cv` (file, optional) - PDF, DOC, or DOCX up to 10MB
- `job_opening_id` (optional)

**Response:**
```json
{
  "success": true,
  "message": "Thank you for your application! We will review it...",
  "id": 789
}
```

## File Upload Configuration

CV files are uploaded to: `storage/uploads/cv/`

**Naming Convention:** `cv_{unique_id}_{timestamp}.{extension}`

**Allowed File Types:** PDF, DOC, DOCX  
**Maximum File Size:** 10MB

## Frontend Implementation

### 1. PDF Email Form (index.php)
- Form ID: `pdfEmailForm`
- AJAX submission with fetch API
- Real-time feedback via `pdfEmailMessage` div
- Success: Green message, form resets
- Error: Red message with error details

### 2. Main Lead Form (leadform.php)
- Standard POST submission to same page
- Server-side validation and database insertion
- Redirect/reload on success with green success message
- Error messages displayed in red

### 3. Job Application Form (careers.php)
- Form ID: `jobApplicationForm`
- AJAX submission with FormData (for file upload)
- File upload preview shows selected filename
- Success: Green message, form resets
- Error: Red message with error details
- Handles CV upload with proper validation

## Security Features

1. **Input Validation**
   - Email format validation
   - URL format validation (portfolio links)
   - Required field checks
   - File type validation

2. **File Upload Security**
   - Allowed extensions whitelist
   - File size limits (10MB)
   - Unique filename generation
   - Stored outside public directory

3. **SQL Injection Prevention**
   - PDO prepared statements used throughout
   - All user input properly escaped

4. **Data Collection**
   - IP address logging for fraud detection
   - User agent tracking
   - Timestamp for all submissions

## Testing the Forms

### Test PDF Email Form:
1. Go to: `http://localhost/digifyce2/`
2. Scroll to Strategy Matrix section
3. Enter email in "Get detailed PDF" form
4. Submit and check for success message
5. Verify entry in `pdf_email_leads` table

### Test Main Lead Form:
1. Go to: `http://localhost/digifyce2/leadform.php`
2. Fill in all required fields (name, email, message)
3. Submit form
4. Page should reload with green success message
5. Verify entry in `lead_form_submissions` table

### Test Job Application Form:
1. Go to: `http://localhost/digifyce2/careers.php`
2. Scroll to "Spontaneous Application" section
3. Fill in all required fields
4. Optionally upload a CV (PDF or DOCX)
5. Submit form
6. Check for success message
7. Verify entry in `job_applications` table
8. If CV uploaded, verify file in `storage/uploads/cv/`

## Database Queries for Admin

### View all PDF email leads:
```sql
SELECT * FROM pdf_email_leads ORDER BY created_at DESC;
```

### View all lead form submissions:
```sql
SELECT * FROM lead_form_submissions ORDER BY created_at DESC;
```

### View all job applications:
```sql
SELECT * FROM job_applications ORDER BY created_at DESC;
```

### Count leads by date:
```sql
SELECT DATE(created_at) as date, COUNT(*) as count 
FROM lead_form_submissions 
GROUP BY DATE(created_at) 
ORDER BY date DESC;
```

### View pending job applications:
```sql
SELECT * FROM job_applications 
WHERE status = 'pending' 
ORDER BY created_at DESC;
```

## Next Steps (Optional Enhancements)

1. **Email Notifications**
   - Send confirmation emails to users
   - Alert admin when new lead/application received
   - Use PHPMailer or similar

2. **Admin Dashboard**
   - Create admin pages to view/manage submissions
   - Add filters and search functionality
   - Export to CSV/Excel

3. **Auto-responders**
   - Send PDF automatically on email submission
   - Send thank you emails for applications

4. **Lead Scoring**
   - Add scoring system based on budget, company size, etc.
   - Priority flagging for high-value leads

5. **Analytics Integration**
   - Track form conversions in Google Analytics
   - Add event tracking for form submissions

## Files Modified

- `schema.sql` - Added 3 new table definitions
- `leadform.php` - Updated to save to database
- `index.php` - Added PDF email form with AJAX
- `careers.php` - Added job application form with file upload

## Files Created

- `app/api/pdf_email_lead.php` - API endpoint for PDF emails
- `app/api/lead_form_submit.php` - API endpoint for lead forms
- `app/api/job_application_submit.php` - API endpoint for job applications
- `lead_forms_tables.sql` - SQL for creating tables
- `storage/uploads/cv/` - Directory for CV uploads

## Support

If you encounter any issues:
1. Check PHP error logs in `C:\xampp\apache\logs\error.log`
2. Verify MySQL connection in `.env` file
3. Ensure write permissions on `storage/uploads/cv/` directory
4. Check browser console for JavaScript errors
5. Test API endpoints directly with Postman or curl

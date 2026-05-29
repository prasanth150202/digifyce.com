# Admin Panel Guide - Lead Forms Management

## Access the Admin Panel

**URL:** `http://localhost/digifyce2/app/admin/login.php`

## New Admin Pages for Lead Forms

### 1. Lead Form Submissions
**URL:** `http://localhost/digifyce2/app/admin/lead_submissions.php`

**Features:**
- View all lead form submissions from leadform.php
- Dashboard with stats (Total, Today, Last 7 Days)
- Detailed view with full information
- Delete submissions
- View contact details: name, email, phone, company, budget, website, message
- IP address and submission timestamp tracking

**Fields Displayed:**
- Full Name
- Email (clickable mailto link)
- Phone (clickable tel link)
- Company
- Budget (displayed as badge)
- Website
- Message
- IP Address
- Submission Date/Time

**Actions:**
- 👁️ View Details - Opens modal with full information
- 🗑️ Delete - Removes submission from database

---

### 2. PDF Email Leads
**URL:** `http://localhost/digifyce2/app/admin/pdf_email_leads.php`

**Features:**
- View all PDF download email requests
- Dashboard with stats (Total, Today, Last 7 Days)
- Export to CSV functionality
- Track source of leads
- Delete individual leads

**Fields Displayed:**
- Date & Time
- Email Address (clickable mailto link)
- Source (badge - e.g., "strategy_matrix")
- IP Address

**Actions:**
- 📥 Export to CSV - Download all leads as CSV file
- 🗑️ Delete - Remove email lead

---

### 3. Job Applications
**URL:** `http://localhost/digifyce2/app/admin/job_applications.php`

**Features:**
- View all job applications with CV downloads
- Dashboard with stats:
  - Total Applications
  - Pending
  - Reviewed
  - Shortlisted
- Status management (Pending/Reviewed/Shortlisted/Rejected)
- CV file downloads
- View full application details including cover letter
- Delete applications (also deletes CV file)

**Fields Displayed:**
- Date & Time
- Applicant Name
- Email (clickable mailto link)
- Portfolio/LinkedIn URL (external link button)
- Job Position (badge - shows job title or "Spontaneous")
- CV Download button
- Status dropdown (editable)

**Status Management:**
- **Pending** (Yellow badge) - New applications
- **Reviewed** (Blue badge) - Applications that have been read
- **Shortlisted** (Green badge) - Candidates moving forward
- **Rejected** (Red badge) - Applications not proceeding

**Actions:**
- 👁️ View Details - Opens modal with full application including cover letter
- 📥 Download CV - Download uploaded resume/CV
- 🔄 Change Status - Use dropdown to update application status
- 🗑️ Delete - Removes application and associated CV file

---

## Dashboard Integration

The admin dashboard (`http://localhost/digifyce2/app/admin/dashboard.php`) now includes:

**New Cards:**
1. **Lead Forms** (Red) - Link to lead_submissions.php
2. **PDF Leads** (Yellow) - Link to pdf_email_leads.php  
3. **Applications** (Gray) - Link to job_applications.php

**Sidebar Navigation:**
All three new pages are accessible from the left sidebar:
- 📧 Lead Submissions
- 📄 PDF Leads
- 👔 Job Applications

---

## Common Features Across All Pages

### Stats Cards
Each page displays real-time statistics at the top:
- Total count
- Today's count
- Last 7 days count

### Responsive Tables
- Mobile-friendly design
- Sortable columns
- Hover effects for better UX

### Modal Popups
- Click "View" button to see full details
- Clean modal design with all information
- Easy to close and return to list

### Delete Confirmation
- JavaScript confirmation before deletion
- Prevents accidental data loss

### Date Formatting
- Consistent date format: "Jan 15, 2026"
- Time display: "3:45 PM"
- Easy to read at a glance

---

## Security Notes

1. **Session Required:** All admin pages check for active session
2. **Login Required:** Redirects to login page if not authenticated
3. **SQL Injection Protection:** All queries use PDO prepared statements
4. **File Security:** CV files stored in protected directory
5. **XSS Protection:** All output properly escaped with htmlspecialchars()

---

## Database Tables

### `lead_form_submissions`
Stores main lead form data with 9 fields + metadata

### `pdf_email_leads`
Stores PDF download email requests with source tracking

### `job_applications`
Stores job applications with CV upload support and status tracking

---

## Tips for Admin Users

1. **Regular Checks:** Check lead submissions daily for timely follow-ups
2. **Status Updates:** Keep job application statuses current
3. **CSV Export:** Regularly export PDF leads for marketing campaigns
4. **CV Downloads:** Download CVs for offline review and sharing
5. **Clean Up:** Periodically delete old/spam submissions

---

## Keyboard Shortcuts & Quick Actions

- **View Modal:** Click anywhere on table row or use 👁️ button
- **Email Contact:** Click email address to open default mail client
- **Call Contact:** Click phone number to initiate call
- **Export CSV:** One-click export on PDF leads page
- **Status Change:** Change status directly from table without opening modal

---

## Troubleshooting

### Can't Access Admin Panel
- Check if logged in: `http://localhost/digifyce2/app/admin/login.php`
- Verify session is active
- Clear browser cookies if issues persist

### CV Files Not Downloading
- Check file permissions on `storage/uploads/cv/` directory
- Verify file exists at specified path
- Check server has read permissions

### Stats Not Showing
- Verify database tables exist
- Check database connection in `.env` file
- Ensure data exists in tables

---

## Future Enhancements (Optional)

- Email notifications on new submissions
- Advanced filtering and search
- Bulk actions (delete multiple, export selected)
- Application notes/comments system
- Email templates for responses
- Analytics dashboard with charts
- Lead scoring system
- CRM integration

---

## Support

For technical issues or feature requests, contact the development team.

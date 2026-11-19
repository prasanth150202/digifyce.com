/**
 * D2C brand enquiry leads -> Google Sheet
 *
 * Receives leads posted by d2c-enquiry.php (via app/utilities/GoogleSheetLead.php)
 * and appends one row per lead to the "D2C Leads" tab.
 *
 * SETUP
 * 1. Open the Google Sheet > Extensions > Apps Script.
 * 2. Replace the editor contents with this file and set SECRET_TOKEN below
 *    to a long random string.
 * 3. Deploy > New deployment > type "Web app":
 *      Execute as:     Me
 *      Who has access: Anyone
 *    Authorize when asked, then copy the Web app URL (ends in /exec).
 * 4. In the website's .env add:
 *      D2C_LEADS_SHEET_WEBHOOK=<the /exec URL>
 *      D2C_LEADS_SHEET_TOKEN=<the same SECRET_TOKEN>
 *
 * After editing this script later, use Deploy > Manage deployments > Edit >
 * Version: New version, so the same URL keeps working.
 */

var SECRET_TOKEN = 'CHANGE_ME_TO_A_LONG_RANDOM_STRING';
var SHEET_NAME = 'D2C Leads';
var HEADERS = ['Submitted At', 'Full Name', 'Contact Number', 'Brand Category', 'Location', 'Additional Details', 'Page URL'];

function doPost(e) {
  var data;
  try {
    data = JSON.parse(e.postData.contents);
  } catch (err) {
    return reply({ ok: false, error: 'bad_json' });
  }
  if (!data || data.token !== SECRET_TOKEN) {
    return reply({ ok: false, error: 'unauthorized' });
  }

  var lock = LockService.getScriptLock();
  lock.waitLock(10000);
  try {
    var ss = SpreadsheetApp.getActiveSpreadsheet();
    var sheet = ss.getSheetByName(SHEET_NAME) || ss.insertSheet(SHEET_NAME);
    if (sheet.getLastRow() === 0) {
      sheet.appendRow(HEADERS);
      sheet.getRange(1, 1, 1, HEADERS.length).setFontWeight('bold');
      sheet.setFrozenRows(1);
    }
    sheet.appendRow([
      safe(data.submitted_at),
      safe(data.full_name),
      safe(data.contact_number),
      safe(data.brand_category),
      safe(data.location),
      safe(data.additional_details),
      safe(data.page_url)
    ]);
  } finally {
    lock.releaseLock();
  }
  return reply({ ok: true });
}

// Store user input as plain text: a leading = + - @ would otherwise be read as
// a formula (and "+91..." phone numbers would lose their plus sign).
function safe(value) {
  var s = value == null ? '' : String(value);
  return /^[=+\-@]/.test(s) ? "'" + s : s;
}

function reply(obj) {
  return ContentService.createTextOutput(JSON.stringify(obj)).setMimeType(ContentService.MimeType.JSON);
}

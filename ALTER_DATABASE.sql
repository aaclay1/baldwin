-- Run this on your database to add JSON storage for all form fields
ALTER TABLE form_submissions ADD COLUMN form_data JSON NULL;

-- This adds ONE column that will store all the form field data as JSON
-- So customer.html data, property.html data, etc. all get stored together

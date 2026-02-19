-- Add role column to users table
ALTER TABLE users ADD COLUMN role ENUM('admin', 'superadmin') DEFAULT 'admin';

-- Set 'admin' user as superadmin (Developer)
UPDATE users SET role = 'superadmin' WHERE username = 'admin';

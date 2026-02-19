-- Dashboard Performance Indexes
-- Run this SQL directly if migration fails

-- Payment Schedules indexes
CREATE INDEX IF NOT EXISTS idx_status_due_date ON payment_schedules (status, due_date);
CREATE INDEX IF NOT EXISTS idx_status_paid_at ON payment_schedules (status, paid_at);
CREATE INDEX IF NOT EXISTS idx_enrollment_id ON payment_schedules (enrollment_id);

-- Students indexes
CREATE INDEX IF NOT EXISTS idx_status ON students (status);

-- Enrollments indexes
CREATE INDEX IF NOT EXISTS idx_status_balance ON enrollments (status, remaining_balance);
CREATE INDEX IF NOT EXISTS idx_student_id_enrollments ON enrollments (student_id);

-- Attendance Records indexes
CREATE INDEX IF NOT EXISTS idx_date_status ON attendance_records (attendance_date, status);
CREATE INDEX IF NOT EXISTS idx_student_id_attendance ON attendance_records (student_id);

-- Verify indexes were created
SELECT 
    tablename, 
    indexname, 
    indexdef 
FROM pg_indexes 
WHERE tablename IN ('payment_schedules', 'students', 'enrollments', 'attendance_records')
AND indexname LIKE 'idx_%'
ORDER BY tablename, indexname;

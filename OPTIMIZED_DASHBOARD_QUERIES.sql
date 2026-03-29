-- ============================================
-- OPTIMIZED DASHBOARD QUERIES
-- ============================================
-- All queries use indexed columns for performance
-- Designed for PostgreSQL on Render

-- ============================================
-- 1. KPI STATS (Single Query)
-- ============================================
-- Returns: total_students, active_students, due_today, overdue, 
--          collected_today, collected_this_month, outstanding_balance
-- Cache: 3 minutes

SELECT 
    (SELECT COUNT(*) FROM students WHERE status = 'ACTIVE') as total_students,
    
    (SELECT COUNT(*) FROM students WHERE status = 'ACTIVE' 
     AND EXISTS (SELECT 1 FROM enrollments WHERE student_id = students.id AND status = 'ACTIVE')) as active_students,
    
    (SELECT COUNT(*) FROM payment_schedules WHERE status = 'UNPAID' AND due_date = CURRENT_DATE) as due_today,
    
    (SELECT COUNT(*) FROM payment_schedules WHERE status = 'UNPAID' AND due_date < CURRENT_DATE) as overdue,
    
    (SELECT COALESCE(SUM(amount_due), 0) FROM payment_schedules 
     WHERE status = 'PAID' AND DATE(paid_at) = CURRENT_DATE) as collected_today,
    
    (SELECT COALESCE(SUM(amount_due), 0) FROM payment_schedules 
     WHERE status = 'PAID' 
     AND EXTRACT(YEAR FROM paid_at) = EXTRACT(YEAR FROM CURRENT_DATE)
     AND EXTRACT(MONTH FROM paid_at) = EXTRACT(MONTH FROM CURRENT_DATE)) as collected_this_month,
    
    (SELECT COALESCE(SUM(remaining_balance), 0) FROM enrollments WHERE status = 'ACTIVE') as outstanding_balance;

-- ============================================
-- 2. FINANCIAL SUMMARY (Single Query)
-- ============================================
-- Returns: revenue_this_month, revenue_last_month, outstanding_balance
-- Cache: 5 minutes

SELECT 
    (SELECT COALESCE(SUM(amount_due), 0) FROM payment_schedules 
     WHERE status = 'PAID' 
     AND EXTRACT(YEAR FROM paid_at) = EXTRACT(YEAR FROM CURRENT_DATE)
     AND EXTRACT(MONTH FROM paid_at) = EXTRACT(MONTH FROM CURRENT_DATE)) as revenue_this_month,
    
    (SELECT COALESCE(SUM(amount_due), 0) FROM payment_schedules 
     WHERE status = 'PAID' 
     AND EXTRACT(YEAR FROM paid_at) = EXTRACT(YEAR FROM CURRENT_DATE - INTERVAL '1 month')
     AND EXTRACT(MONTH FROM paid_at) = EXTRACT(MONTH FROM CURRENT_DATE - INTERVAL '1 month')) as revenue_last_month,
    
    (SELECT COALESCE(SUM(remaining_balance), 0) FROM enrollments WHERE status = 'ACTIVE') as outstanding_balance;

-- ============================================
-- 3. ATTENDANCE SUMMARY (Single Query)
-- ============================================
-- Returns: present_today, absent_today, late_today, excused_today
-- Cache: 3 minutes

SELECT 
    (SELECT COUNT(*) FROM attendance_records 
     WHERE DATE(attendance_date) = CURRENT_DATE AND status = 'PRESENT') as present_today,
    
    (SELECT COUNT(*) FROM attendance_records 
     WHERE DATE(attendance_date) = CURRENT_DATE AND status = 'ABSENT') as absent_today,
    
    (SELECT COUNT(*) FROM attendance_records 
     WHERE DATE(attendance_date) = CURRENT_DATE AND status = 'LATE') as late_today,
    
    (SELECT COUNT(*) FROM attendance_records 
     WHERE DATE(attendance_date) = CURRENT_DATE AND status = 'EXCUSED') as excused_today;

-- ============================================
-- 4. ALERTS (Single Query)
-- ============================================
-- Returns: overdue_count, due_soon_count, missing_attendance_count
-- Cache: 3 minutes

SELECT 
    (SELECT COUNT(*) FROM payment_schedules 
     WHERE status = 'UNPAID' AND due_date < CURRENT_DATE) as overdue_count,
    
    (SELECT COUNT(*) FROM payment_schedules 
     WHERE status = 'UNPAID' 
     AND due_date BETWEEN CURRENT_DATE AND CURRENT_DATE + INTERVAL '7 days') as due_soon_count,
    
    (SELECT COUNT(*) FROM students 
     WHERE status = 'ACTIVE' 
     AND NOT EXISTS (
         SELECT 1 FROM attendance_records 
         WHERE attendance_records.student_id = students.id 
         AND DATE(attendance_date) = CURRENT_DATE
     )) as missing_attendance_count;

-- ============================================
-- 5. COLLECTIONS TREND (Last 30 Days)
-- ============================================
-- Returns: date, total for each day
-- Cache: 5 minutes

SELECT 
    DATE(paid_at) as date,
    SUM(amount_due) as total
FROM payment_schedules
WHERE status = 'PAID'
  AND paid_at >= CURRENT_DATE - INTERVAL '29 days'
  AND paid_at <= CURRENT_DATE + INTERVAL '1 day'
GROUP BY DATE(paid_at)
ORDER BY date;

-- ============================================
-- 6. RECENT PAYMENTS (Last 10)
-- ============================================
-- Returns: payment details with student and package info
-- Cache: 3 minutes

SELECT 
    ps.paid_at,
    s.student_no,
    CONCAT(s.first_name, ' ', s.last_name) as student_name,
    p.name as package_name,
    ps.installment_no,
    ps.amount_due,
    ps.payment_method
FROM payment_schedules ps
JOIN enrollments e ON ps.enrollment_id = e.id
JOIN students s ON e.student_id = s.id
JOIN packages p ON e.package_id = p.id
WHERE ps.status = 'PAID'
  AND DATE(ps.paid_at) >= CURRENT_DATE - INTERVAL '7 days'
ORDER BY ps.paid_at DESC
LIMIT 10;

-- ============================================
-- REQUIRED INDEXES
-- ============================================
-- These indexes are created by the migration
-- Verify with: \d+ table_name in psql

-- students
CREATE INDEX IF NOT EXISTS idx_students_status ON students(status);

-- enrollments
CREATE INDEX IF NOT EXISTS idx_enrollments_status ON enrollments(status);
CREATE INDEX IF NOT EXISTS idx_enrollments_status_balance ON enrollments(status, remaining_balance);

-- payment_schedules
CREATE INDEX IF NOT EXISTS idx_ps_status ON payment_schedules(status);
CREATE INDEX IF NOT EXISTS idx_ps_due_date ON payment_schedules(due_date);
CREATE INDEX IF NOT EXISTS idx_ps_paid_at ON payment_schedules(paid_at);
CREATE INDEX IF NOT EXISTS idx_ps_status_paid_at ON payment_schedules(status, paid_at);
CREATE INDEX IF NOT EXISTS idx_ps_status_due_date ON payment_schedules(status, due_date);

-- attendance_records
CREATE INDEX IF NOT EXISTS idx_attendance_date ON attendance_records(attendance_date);
CREATE INDEX IF NOT EXISTS idx_attendance_status ON attendance_records(status);
CREATE INDEX IF NOT EXISTS idx_attendance_date_status ON attendance_records(attendance_date, status);

-- ============================================
-- QUERY PERFORMANCE TIPS
-- ============================================

-- 1. Always use indexed columns in WHERE clauses
-- 2. Use COUNT(*) instead of COUNT(column) when possible
-- 3. Use EXISTS instead of IN for subqueries
-- 4. Use COALESCE for NULL handling in SUM
-- 5. Use DATE() function consistently for date comparisons
-- 6. Limit result sets to minimum needed (LIMIT 10)
-- 7. Use composite indexes for multi-column WHERE clauses
-- 8. Avoid SELECT * - specify only needed columns
-- 9. Use EXPLAIN ANALYZE to verify query plans
-- 10. Cache results for 3-5 minutes

-- ============================================
-- VERIFY INDEX USAGE
-- ============================================
-- Run EXPLAIN ANALYZE to verify indexes are being used

EXPLAIN ANALYZE
SELECT COUNT(*) FROM payment_schedules 
WHERE status = 'UNPAID' AND due_date < CURRENT_DATE;

-- Should show "Index Scan" not "Seq Scan"

-- ============================================
-- CACHE INVALIDATION TRIGGERS
-- ============================================

-- When payment is recorded:
-- - Clear: dashboard_kpi_stats_v3
-- - Clear: dashboard_collections_trend_v3
-- - Clear: dashboard_alerts_v3
-- - Clear: dashboard_recent_payments_v3
-- - Clear: dashboard_financial_summary_v1

-- When attendance is recorded:
-- - Clear: dashboard_attendance_summary_v1
-- - Clear: dashboard_alerts_v3

-- When student status changes:
-- - Clear: dashboard_kpi_stats_v3
-- - Clear: dashboard_alerts_v3

-- Manual refresh:
-- - Clear: ALL caches

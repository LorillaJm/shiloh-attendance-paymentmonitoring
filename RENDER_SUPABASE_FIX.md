# Fix Supabase Connection Timeout on Render

## 🔴 Problem

You're getting: `connection to server at "aws-1-ap-southeast-1.pooler.supabase.com", port 6543 failed: timeout expired`

This happens because:
1. Render's free tier has network restrictions
2. Supabase's pooler (port 6543) times out from Render
3. You need to use the DIRECT connection (port 5432)

## ✅ Solution

### Step 1: Update Render Environment Variables

Go to your Render dashboard → Your web service → Environment tab

**Change these variables:**

```
DB_PORT=5432
```

**NOT** `6543` (the pooler port)

### Step 2: Verify Other Database Settings

Make sure these are set correctly in Render:

```
DB_CONNECTION=pgsql
DB_HOST=aws-1-ap-southeast-1.pooler.supabase.com
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres.lggzjlevfmqlqhqoinwh
DB_PASSWORD=116161Shiloh2026
DB_SSLMODE=require
DB_CONNECT_TIMEOUT=10
```

### Step 3: Save and Redeploy

1. Click "Save Changes" in Render
2. Render will automatically redeploy
3. Wait 2-3 minutes for deployment to complete

### Step 4: Test Connection

Once deployed, visit:
```
https://shiloh-attendance-paymentmonitoring.onrender.com/up
```

Should return: `{"status":"ok"}`

Then try:
```
https://shiloh-attendance-paymentmonitoring.onrender.com/admin
```

## 🎯 Why This Works

- **Port 5432** = Direct connection to Supabase database
- **Port 6543** = Pooler (connection pooling service)

Render's free tier can't reliably connect to the pooler, but CAN connect directly to port 5432.

## 📊 Performance Impact

Direct connection (5432):
- ✅ Works reliably from Render
- ✅ No timeout issues
- ⚠️ Slightly slower (no pooling)
- ✅ Still fast enough for your app

Pooler connection (6543):
- ❌ Times out from Render free tier
- ✅ Faster (with pooling)
- ❌ Not accessible from Render

## 🔍 Verify It's Working

After changing to port 5432, check Render logs. You should see:
```
✅ Database migrations completed
✅ Caches rebuilt
🎉 Build completed successfully!
```

Instead of:
```
❌ connection timeout expired
```

## 🚨 If Still Not Working

### Option 1: Check Supabase IP Restrictions

1. Go to Supabase dashboard
2. Settings → Database
3. Check if there are IP restrictions
4. Add Render's IP ranges if needed (or allow all for testing)

### Option 2: Use Supabase Direct URL

In Render, you can also use the full connection string:

```
DB_URL=postgresql://postgres.lggzjlevfmqlqhqoinwh:116161Shiloh2026@aws-1-ap-southeast-1.pooler.supabase.com:5432/postgres?sslmode=require
```

Then remove individual DB_* variables.

### Option 3: Switch to Render PostgreSQL

If Supabase continues to have issues, consider using Render's own PostgreSQL:

1. In Render dashboard, create a new PostgreSQL database
2. Link it to your web service
3. Render will auto-populate DB_* variables
4. Much more reliable for Render-to-Render connections

## ✅ Expected Result

After fixing, your dashboard should:
- Load in <1 second
- No 500 errors
- No connection timeouts
- All widgets display correctly

---

**Current Status:** Waiting for you to change `DB_PORT` from `6543` to `5432` in Render environment variables.

# Redis Setup & Configuration Guide

**Status:** ✅ Installed & Running
**Location:** `c:\xampp\htdocs\tools\redis`

---

## 1. How It Was Installed

I performed a **Portable Installation** to avoid messing with your system permissions or "Program Files".

1. **Created Directory:** `c:\xampp\htdocs\tools\redis`
2. **Downloaded:** [Redis-x64-5.0.14.1.zip](https://github.com/tporadowski/redis/releases) (Community Windows Port)
3. **Extracted:** Unzipped all files into that folder.

---

## 2. How to Run Automatically (Windows Service)

To make Redis run automatically in the background when you turn on your PC, you should install it as a **Windows Service
**.

I have already downloaded the necessary files. You just need to run one command.

### Step-by-Step for Auto-Start

1. Open a **Terminal** as **Administrator** (Right-click Terminal -> Run as Admin).
2. Run the following command:

    ```cmd
    c:\xampp\htdocs\tools\redis\redis-server.exe --service-install c:\xampp\htdocs\tools\redis\redis.windows.conf --service-name Redis
    ```

3. Start the service:

    ```cmd
    net start Redis
    ```

**That's it!** Redis will now run silently in the background every time Windows starts.

*(If you don't want a service, you can just double-click `redis-server.exe` inside `tools/redis` whenever you need it.)*

---

## 3. How to Connect & Configure

### Laravel Configuration (.env)

I have already updated your `.env` file to use Redis.

```ini
# .env file
REDIS_HOST = 127.0.0.1
REDIS_PASSWORD = null
REDIS_PORT = 6379

QUEUE_CONNECTION = redis  <-- Critical for background emails
CACHE_DRIVER = redis      <-- Makes the app faster
SESSION_DRIVER = redis    <-- (Optional) Handles user sessions faster
```

### Viewing Redis Data (GUI)

To see what's inside Redis (queued jobs, cache keys), you can use a GUI tool:

1. **Redis Insight** (Official, Free): [Download Here](https://redis.com/redis-enterprise/redis-insight/)
2. **Another Redis Desktop Manager**: [Download Here](https://github.com/qishibo/AnotherRedisDesktopManager)

**Connection Details for GUI:**

- **Host:** `127.0.0.1`
- **Port:** `6379`
- **Name:** Local Redis
- **Password:** (Leave Empty/None)

---

## 4. Processing the Queue

Since we switched `QUEUE_CONNECTION` to `redis`, **emails will sit in the Redis queue until a worker processes them.**

To process them during development, run:

```bash
php artisan queue:work
```

Keep this terminal window open while you test features like Registration or Password Reset.

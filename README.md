# 🚀 WHMCS Upcoming Expiry Notification

A lightweight WHMCS extension that detects upcoming service expiries and displays them inside the Admin Dashboard using a custom widget and hook system.

This module helps hosting providers monitor renewals and reduce missed expiry revenue.

---

## 📌 Features

- 🔔 Detects upcoming service expiries automatically
- 📊 Admin Dashboard widget for quick overview
- ⚡ Lightweight & optimized queries
- 🧩 No WHMCS core file modification required
- 🛡 Safe for production use

---

## 📂 Module Structure

```
whmcs-upcoming-expiry-notification/
│
├── includes/
│   └── hooks/
│       └── upcoming_expires.php
│
└── modules/
    └── widgets/
        └── UpcomingExpiries.php
```

---

## ⚙️ Installation

1. Extract the module files.
2. Upload contents to your WHMCS root directory.

Final structure should be:

```
/includes/hooks/upcoming_expires.php
/modules/widgets/UpcomingExpiries.php
```

3. Login to WHMCS Admin.
4. Navigate to:

```
Admin Dashboard → Configure Widgets
```

5. Enable **Upcoming Expiries** widget.

---

## 🕒 Cron Requirement

This module depends on WHMCS daily cron execution.

Ensure your cron is properly configured:

```
php -q /path/to/whmcs/crons/cron.php
```

Recommended: Run once per day.

---

## 🧠 How It Works

- The hook scans for active services.
- It checks the `nextduedate` field.
- Services nearing expiry are collected.
- The widget displays the upcoming expiring services inside the Admin Dashboard.

---

## 📊 Compatibility

- WHMCS 8.x+
- PHP 7.4 – 8.2
- MySQL 5.7+

---

## 🔐 Security

- No external API calls
- No core file modification
- Uses WHMCS internal structure
- Production-safe

---

## ⭐ Future Improvements

- Email reminder automation
- WhatsApp notification integration
- Admin configuration panel
- Renewal analytics dashboard

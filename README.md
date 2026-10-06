# 🐠 AquaShop — Vulnerability Demonstration Platform

An intentionally vulnerable LAMP web application built as a **Capstone Project for 20CYS403 Web Application Security**.

AquaShop runs as a normal-looking aquarium e-commerce site, but includes a **session-based toggle** that flips the entire application between **VULNERABLE** and **SECURE** modes. Every vulnerability has:

- Vulnerable code (demo)
- Attack (working payload)
- Defense (secure code)
- Documentation in `docs/`

---

## 🎚 Toggle

Click the pill in the navbar to switch modes. The mode is stored in `$_SESSION['mode']`.

| Pill | Mode | Behaviour |
|------|------|-----------|
| ⚠️ Red | `vulnerable` | Intentionally insecure |
| 🛡️ Green | `secure` | Hardened |

Default: **vulnerable** (for demo).

---

## 🔴 Vulnerabilities Demonstrated

| # | Name | Location | Doc |
|---|------|----------|-----|
| 01 | SQL Injection — Login | login.php | docs/01-sqli-login.md |
| 02 | SQL Injection — Search | products.php?search= | docs/02-sqli-search.md |
| 03 | Stored XSS | product name/description | docs/03-stored-xss.md |
| 04 | Reflected XSS | products.php?search= | docs/04-reflected-xss.md |
| 05 | IDOR — Address | address-edit.php?id= | docs/05-idor-address.md |
| 06 | IDOR — Order | order-details.php?id= | docs/06-idor-order.md |
| 07 | CSRF | address-delete, order-update | docs/07-csrf.md |
| 08 | Clickjacking | any page | docs/08-clickjacking.md |
| 09 | DOM-based XSS | products.php?highlight= | docs/09-dom-xss.md |
| 10 | Path Traversal | product.php?img= | docs/10-path-traversal.md |
| 11 | Broken Access Control | /admin/* | docs/11-bac.md |
| 12 | Unrestricted File Upload | admin/add-product.php | docs/12-file-upload.md |

---

## 🚀 Quick Start

    git clone <repo>
    cd aquashop

    # Create DB
    mysql -u root -p
    CREATE DATABASE aquashop;
    mysql aquashop < database/schema.sql
    mysql aquashop < database/seed.sql

    # Configure credentials
    cp app/config/config.local.php.example app/config/config.local.php
    # edit app/config/config.local.php with your DB creds

    # Serve
    php -S localhost:8000 -t public

Open http://localhost:8000

---

## 🧪 Testing

Each vulnerability has a corresponding markdown file in `docs/` describing:

1. Vulnerable code
2. Attack payload
3. Why it works
4. Defense (secure code)
5. Verification steps

See `docs/` for the complete list.

---

## 🔐 Security Topics Covered

- SQL Injection (auth bypass + UNION + error-based)
- Cross-Site Scripting (stored, reflected, DOM-based)
- Insecure Direct Object Reference (IDOR)
- Cross-Site Request Forgery (CSRF)
- Clickjacking (UI redressing)
- Path Traversal
- Broken Access Control
- Remote Code Execution via unrestricted file upload

Mapped to **20CYS403** syllabus + Quiz 2 topics (CSRF, Clickjacking, SSRF theory).

---

## 💻 Stack

- PHP 8.3
- MySQL 8 / MariaDB
- Apache (or PHP built-in server)
- No JS frameworks — vanilla JS

---

## 📜 License

Educational use only. Do NOT deploy in production.

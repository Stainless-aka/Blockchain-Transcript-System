# Blockchain-Based Student Transcript Verification System

A secure, full-stack PHP 8 MVC web application that uses SHA-256 blockchain hash chaining to
ensure academic transcript integrity and enable public verification.

---

## Features

- **Authentication** — Secure admin login with session management, password hashing (bcrypt), and CSRF protection
- **Dashboard** — Real-time stats for students, transcripts, blockchain blocks, and verification status
- **Student CRUD** — Full create, read, update, delete with search, pagination, and validation
- **Transcript CRUD** — Create transcripts with automated SHA-256 hashing and blockchain anchoring
- **Blockchain Engine** — Custom simulated blockchain: genesis block, hash chaining, nonce proof-of-work, chain validation
- **Public Verification Portal** — Anyone can verify a transcript by ID or verification code
- **Activity Audit Log** — Every action (login, CRUD, verification) is tracked with user and IP
- **Responsive UI** — Bootstrap 5 + Bootstrap Icons, fully mobile-friendly
- **Dark Mode** — Toggle dark/light mode with localStorage persistence
- **PDF Upload** — Optional PDF attachment to transcripts

---

## Tech Stack

| Layer        | Technology              |
|-------------|--------------------------|
| Language    | PHP 8+                   |
| Database    | MySQL 5.7+ / MariaDB     |
| DB Access   | PDO (Prepared Statements)|
| Frontend    | Bootstrap 5, Vanilla JS  |
| Icons       | Bootstrap Icons 1.11     |
| Architecture| Custom MVC (no framework)|
| Hashing     | SHA-256 (native PHP)     |
| Server      | Apache / XAMPP           |

---

## Installation

### 1. Clone / Extract the Project

Place the `professional-starter-template` folder inside your XAMPP `htdocs`:

```
C:\xampp\htdocs\professional-starter-template\
```

### 2. Import the Database

Open **phpMyAdmin**, then:
1. Create a database named `transcript_verification`
2. Import `database/transcript_verification.sql`

Or via CLI:
```bash
mysql -u root -p transcript_verification < database/transcript_verification.sql
```

### 3. Configure the Environment

Edit `.env` in the project root:

```env
DB_HOST=localhost
DB_NAME=transcript_verification
DB_USER=root
DB_PASS=your_password

APP_URL=http://localhost/professional-starter-template/public
APP_ENV=development
```

### 4. Start Apache

Start Apache (and MySQL) via XAMPP Control Panel.

### 5. Access the Application

- **Admin Panel:** `http://localhost/professional-starter-template/public`
- **Verify Transcript:** `http://localhost/professional-starter-template/public/verify`

---

## Default Login Credentials

| Username | Password  |
|----------|-----------|
| admin    | admin123  |

> **Important:** Change the admin password immediately after first login.

---

## Folder Structure

```
professional-starter-template/
├── app/
│   ├── controllers/        # AuthController, StudentController, TranscriptController, etc.
│   ├── helpers/            # Global helper functions
│   ├── middleware/         # AuthMiddleware (protects routes)
│   ├── models/             # User, Student, Transcript, Block, ActivityLog, VerificationLog
│   ├── services/           # BlockchainService, HashService, AuthService
│   └── views/
│       ├── auth/           # login.php, profile.php
│       ├── blockchain/     # index.php
│       ├── dashboard/      # index.php
│       ├── errors/         # 404.php
│       ├── layouts/        # main.php, auth.php, public.php, header, sidebar, footer
│       ├── students/       # index, create, edit
│       ├── transcripts/    # index, create, view
│       └── verification/   # index.php
├── config/
│   └── database.php
├── core/
│   ├── App.php             # Bootstrapper, autoloader, session init
│   ├── Controller.php      # Base controller
│   ├── Database.php        # PDO singleton
│   ├── Model.php           # Base model
│   └── Router.php          # MVC router with middleware support
├── database/
│   └── transcript_verification.sql
├── public/
│   ├── assets/
│   │   ├── css/style.css
│   │   └── js/app.js
│   ├── .htaccess
│   └── index.php           # Application entry point
├── routes/
│   └── web.php
├── .env
├── .gitignore
├── composer.json
└── README.md
```

---

## Blockchain Design

This system implements a **simulated blockchain** — not a decentralized network — using:

1. **Genesis Block** (index 0) — automatically created on first transcript
2. **Hash Chaining** — each block stores the `previous_hash` of the prior block
3. **SHA-256 Hashing** — `hash(block_index + previous_hash + timestamp + data + nonce)`
4. **Proof of Work** — nonce is mined until the hash starts with `"00"` (2-zero difficulty)
5. **Tamper Detection** — recomputing any block's hash will break the chain linkage

### Verification Flow

```
User submits Transcript ID / Verification Code
        ↓
Transcript fetched from database
        ↓
SHA-256 recomputed from live transcript fields
        ↓
Compared against stored hash
        ↓
Block located in blockchain
        ↓
Chain integrity validated (all blocks re-verified)
        ↓
Result: VERIFIED ✓  or  TAMPERED ✗
```

---

## Screenshots


- Login Page
- Dashboard
- Student Management
- Transcript Creation
- Blockchain Explorer
- Public Verification Portal

---

## Security Features

- Passwords hashed with `bcrypt` (cost 12)
- CSRF tokens on every POST form
- Input sanitization (`htmlspecialchars`, `strip_tags`)
- PDO prepared statements (no SQL injection)
- Session regeneration on login
- Session timeout (configurable in `.env`)
- `HttpOnly` session cookies
- `.htaccess` blocks direct directory listings

---

## Future Improvements

- [ ] QR code generation for transcript verification URLs
- [ ] Email notifications on transcript creation/verification
- [ ] PDF export of transcripts using a library like TCPDF or DomPDF
- [ ] Multi-institution support with institution roles
- [ ] REST API endpoints for third-party verification
- [ ] Two-factor authentication (2FA) for admin accounts
- [ ] Blockchain mining difficulty adjustment UI
- [ ] Bulk transcript import via CSV
- [ ] Advanced analytics dashboard with charts

---

## License

This project is licensed under the **MIT License** — free to use for academic and educational purposes.

---

## Academic Context

This system was developed as a **Final Year Project** to demonstrate:

- Practical application of blockchain concepts (without cryptocurrency)
- Secure PHP MVC architecture
- SHA-256 cryptographic integrity guarantees
- Full-stack web development with professional code quality

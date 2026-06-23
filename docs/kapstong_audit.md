# Kapstong OJT Tracker — Architecture, Security & Structure Audit

**Scope note:** This audit is based on your folder/file tree (`kapstong_tree.txt`), since I worked from the structure rather than the source code itself. Findings about *structure, naming, and architecture* are direct observations. Findings under *Security* are **risk areas to verify**, inferred from filenames and patterns — not confirmed vulnerabilities. Treat them as a checklist to go test against your real code, not a verdict.

Excluded from this audit: `PHPMailer/`, `phpqrcode/`, and the full `thecodingmachine/safe` vendor tree — these are third-party Composer dependencies, not your code.

---

## 1. What Your Tree Tells Me About the Project

Your own code lives in:

```
kapstong/
├── composer.json / composer.lock
├── index.php
├── css/            (global + admin/student/supervisor subfolders)
├── js/             (global + admin/student/supervisor subfolders)
├── images/
├── kapstongImage/  (uploads + AI-generated assets + logo)
└── php/
    ├── (24 root-level files: auth, OTP, RFID, mailer, QR, etc.)
    ├── admin/functions/      (37 files)
    ├── auth/                 (4 files — admin_auth, student_auth, supervisor_auth, auth_guard)
    ├── rfid_functions/       (3 files)
    ├── student/student_functions/ (12 files)
    ├── student/subStudent/   (4 files)
    └── supervisor/functions/ (24 files)
```

This is a **3-role attendance/task tracking system** (Admin, Student, Supervisor) with RFID-based clock-in, OTP-based password reset/signup, email notifications, QR generation, and PDF/report exports. That's a legitimately complex system for a student project — good portfolio material once it's organized.

---

## 2. Current Structure Audit

| # | Issue | Why It's a Problem | Severity | Fix |
|---|---|---|---|---|
| 1 | **Flat 24-file dump in `php/` root** (`loginPhase.php`, `mailer.php`, `scan.php`, `verifyOTP.php`, etc., all side-by-side) | No separation between auth, email, RFID, and core utility logic. New devs (or future you) can't tell what's safe to touch without reading every file. | High | Split into `auth/`, `mail/`, `rfid/`, `otp/`, `qr/` subfolders by responsibility (detailed in §3). |
| 2 | **Two RFID registration files**: `registerRFID.php` and `register_rfid.php` in the same folder | Near-identical names, inconsistent casing convention (camelCase vs snake_case) in the *same directory*. Strong signal of duplicate/dead code — one is likely an abandoned draft. | High | Diff the two files. Delete the unused one; if both are genuinely needed, rename to be unambiguous (e.g. `registerRfidCard.php` vs `registerRfidBulkImport.php`). |
| 3 | **Mixed naming convention across the whole project**: `loginPhase.php` (camelCase) vs `sessionTimeOut.php` (camelCase but inconsistent capitalization of "Out") vs `download_attendance_report.php` (snake_case) vs `rfid_login.php` (snake_case) — sometimes in the *same folder* | Inconsistent naming makes the codebase feel unplanned and is the #1 thing a code reviewer notices in the first 30 seconds. | Medium–High | Pick one convention (recommend `snake_case.php` for PHP files — it's the PSR/Laravel-adjacent norm for procedural PHP) and rename everything. |
| 4 | **Two near-identical "test" mailer files**: `sendEmail.php` and `sendEmailTest.php`, plus `testpdf.php`, `rfid_test.php` sitting in production folders | Test/scratch files committed alongside production logic. If these ever reach a real server, they're an easy attack surface (often missing the auth checks that production endpoints have). | High (security-adjacent) | Move all `*test*`/`*Test*` files to a `tests/` directory or delete them before any deployment. Never ship test endpoints in `php/` alongside production ones. |
| 5 | **No `config/` folder** — `kapstongConnection.php` (DB connection) sits loose in `php/` root | DB credentials are almost certainly hardcoded directly in this file if there's no separate config layer (common pattern, also the #1 security finding below). | Critical | Extract credentials to a `.env` file + `config/database.php` that reads from environment variables. |
| 6 | **No `routes/` or front-controller pattern** — every PHP file appears to be directly web-accessible | Every file in `php/admin/functions/`, `php/student/student_functions/`, etc. is presumably hit directly via its own URL (e.g. `/php/admin/functions/approveStudent.php`). This means **every single file must individually re-implement its own auth check**, and if even one forgets, that's a broken-access-control hole. | Critical | See Architecture section — introduce a single entry point or at minimum a mandatory `require_once` guard pattern enforced consistently. |
| 7 | **No `tests/` directory for your own code** | 100+ PHP files, zero automated tests. Any refactor (including the one this audit recommends) is high-risk without tests to catch regressions. | Medium | Add at minimum a handful of PHPUnit tests around auth and RFID logic before restructuring. |
| 8 | **No `docs/` directory** | No `ARCHITECTURE.md`, `DATABASE.md`, `API.md` — for a project this size, that's a real gap for anyone (including future-you in 6 months) trying to onboard. | Medium | Add `docs/` per §9. |
| 9 | **Uploads (`kapstongImage/`) and static assets in the same top-level visibility as source code** | If `kapstongImage/` is where `profile_upload.php` and `upload_documents_action.php` write user-uploaded files, and it's sitting at the project root rather than under a dedicated `public/uploads/` or `storage/` path, uploaded files may be **directly web-executable** depending on server config — a classic file-upload-to-RCE vector if extension filtering is weak. | Critical (verify) | Move uploads outside the web root if possible, or into `storage/uploads/` served only through a PHP script that checks auth + re-validates file type before streaming. |
| 10 | **Inconsistent function-folder naming**: `admin/functions/`, `supervisor/functions/`, but `student/student_functions/` (redundant prefix) | Minor, but it's the kind of inconsistency that signals the project grew organically without a plan. | Low | Rename to `student/functions/` for consistency with the other two roles. |
| 11 | **`exit` as a literal filename in project root** | Either a stray accidental file (e.g. redirected shell output) or a leftover debug artifact. Either way it shouldn't be in the repo. | Low | Delete it. |
| 12 | **AI-generated images named `Gemini_Generated_Image_*.png`** committed directly into `kapstongImage/` | Not a security issue, but worth a naming pass before this goes on GitHub — generic generated filenames look unpolished in a portfolio repo. | Low | Rename to descriptive names (`hero-banner.png`, `login-illustration.png`) before publishing. |

---

## 3. Recommended Folder Structure

### Current (as-is)

```
kapstong/
├── composer.json
├── composer.lock
├── exit
├── index.php
├── css/...
├── images/...
├── js/...
├── kapstongImage/...
└── php/
    ├── [24 mixed-purpose files]
    ├── admin/functions/        [37 files]
    ├── auth/                   [4 files]
    ├── rfid_functions/         [3 files]
    ├── student/student_functions/  [12 files]
    ├── student/subStudent/     [4 files]
    └── supervisor/functions/   [24 files]
```

### Recommended

```
kapstong/
├── .env.example
├── .gitignore
├── composer.json
├── composer.lock
├── README.md
├── LICENSE
│
├── config/
│   ├── database.php          # reads DB creds from .env, no hardcoded values
│   ├── mail.php               # SMTP config from .env
│   └── app.php                # site-wide constants (timezone, session lifetime)
│
├── public/                    # ONLY this folder is web-root
│   ├── index.php
│   ├── css/
│   │   ├── shared/
│   │   ├── admin/
│   │   ├── student/
│   │   └── supervisor/
│   ├── js/
│   │   ├── shared/
│   │   ├── admin/
│   │   ├── student/
│   │   └── supervisor/
│   └── images/
│
├── src/
│   ├── Auth/
│   │   ├── admin_auth.php
│   │   ├── student_auth.php
│   │   ├── supervisor_auth.php
│   │   └── auth_guard.php
│   │
│   ├── Otp/
│   │   ├── checkResetOTP.php
│   │   ├── resendOTP.php
│   │   ├── resendResetOTP.php
│   │   ├── sendResetOTP.php
│   │   ├── verifyOTP.php
│   │   └── verifyResetOTP.php
│   │
│   ├── Rfid/
│   │   ├── rfid_login.php
│   │   ├── registerRFID.php       # ⚠ confirm this is the one you keep, delete the duplicate
│   │   ├── download_today_attendance.php
│   │   ├── emergency_timeout.php
│   │   └── get_live_attendance.php
│   │
│   ├── Mail/
│   │   ├── mailer.php
│   │   └── sendEmail.php
│   │
│   ├── Qr/
│   │   └── generateQr.php
│   │
│   ├── Session/
│   │   ├── sessionTimeOut.php
│   │   ├── loginPhase.php
│   │   ├── logoutPhase.php
│   │   └── starts.php
│   │
│   ├── Password/
│   │   ├── forgotPassword.php
│   │   ├── createNewPassword.php
│   │   └── updateNewPassword.php
│   │
│   ├── Signup/
│   │   ├── signupStudent.php
│   │   └── checkStudentID.php
│   │
│   ├── Shared/
│   │   ├── functions.php
│   │   ├── kapstongConnection.php
│   │   ├── getCourses.php
│   │   └── auto_void_attendance.php
│   │
│   ├── Admin/
│   │   ├── adminDashboard.php
│   │   └── functions/          # same 37 files, unchanged internally
│   │
│   ├── Student/
│   │   ├── studentDashboard.php
│   │   ├── functions/           # renamed from student_functions/
│   │   └── subStudent/
│   │
│   └── Supervisor/
│       ├── supervisorDashboard.php
│       └── functions/
│
├── storage/
│   ├── uploads/                 # profile photos, student documents — NOT web-executable
│   ├── logs/
│   └── cache/
│
├── tests/
│   ├── Auth/
│   ├── Rfid/
│   └── fixtures/
│
├── docs/
│   ├── ARCHITECTURE.md
│   ├── DATABASE.md
│   ├── INSTALLATION.md
│   ├── SECURITY.md
│   └── API.md
│
└── vendor/                      # composer-managed, gitignored
```

**Why each decision:**

- **`public/` as the only web-exposed folder** — this is the single highest-impact change available to you. Right now (based on the tree), your PHP logic files appear directly reachable by URL. Moving everything except `public/` outside the web root (or, if shared hosting forces everything under one root, at minimum blocking direct access via `.htaccess`/`index.php` guards) closes off the broken-access-control risk in finding #6 above.
- **`src/` grouped by *responsibility* (Auth, Otp, Rfid, Mail...) not by role** for shared/cross-cutting concerns, but **kept grouped by *role* (Admin, Student, Supervisor)** for dashboard-specific logic. This hybrid reflects what your code already naturally is — RFID and OTP logic isn't really "admin" or "student" logic, it's infrastructure all three roles touch.
- **`storage/uploads/` outside `public/`** — directly fixes finding #9. User-uploaded files become inert (non-executable) data instead of potential attack payloads.
- **`config/` centralizes secrets** — fixes finding #5. One file change instead of hunting through 24+ files for a hardcoded credential.
- **`tests/` for your code, separate from vendor's own `tests/`** — keeps the distinction clean once this is on GitHub.

---

## 4. Architecture Review

| Architecture | Fit for This Project | Verdict |
|---|---|---|
| **Pure MVC** | Would require a real router + controller layer. Your current code is closer to "one PHP file = one endpoint," which is *not* MVC even though folders are named `functions/`. Adopting full MVC (Laravel-style) would mean rewriting almost everything — too large a leap for the current codebase. | Not recommended right now |
| **Feature-Based** | Group by feature (e.g. `attendance/`, `evaluation/`, `task-management/`) cutting across roles. Elegant in theory, but your system's real boundary is *role* (admin/student/supervisor have almost entirely separate permission models and dashboards), so feature-based would actually fight against your natural structure. | Not recommended |
| **Domain-Driven Design (DDD)** | Significant overkill for a student OJT project. DDD shines in large team, long-lived enterprise systems with complex business rules. Yours has clear, simple roles and CRUD-heavy operations. | Not recommended (yet) |
| **Layered Architecture** (presentation / business logic / data access) | Partially present already — you have a `functions/` layer separate from dashboards. Fully embracing this means separating raw SQL out of your function files into a dedicated data-access layer. Good direction, but on its own doesn't address the role-based folder organization you already have. | Good complementary practice, not sufficient alone |
| **Modular Monolith, organized by Role + Shared Infrastructure** ✅ | This matches what you're already doing — three roles (Admin/Student/Supervisor) each with their own dashboard + functions, plus genuinely shared infrastructure (Auth, OTP, RFID, Mail, QR). Formalizing this (the `src/` structure above) gets you 80% of the organizational benefit of "real" architecture patterns with a fraction of the rewrite. | **Recommended** |

**Why this is the right call for you specifically:** you're a 4th-year IT student about to start OJT — the goal right now is a codebase that's *legible, secure, and demonstrably organized* for a portfolio/interview context, not a textbook DDD implementation. Recommending DDD here would be architecture-resume-padding, not a genuine improvement to a system of this size. The Modular Monolith framing is also a great talking point in interviews: it shows you understand *why* you didn't over-engineer it.

---

## 5. Naming Convention Review

| Element | Current State (observed) | Recommended Standard |
|---|---|---|
| **PHP files** | Mixed: `loginPhase.php`, `download_attendance_report.php`, `rfid_login.php` | Pick one: **`snake_case.php`** for all files (matches majority of your `php/admin/functions/` already) |
| **Folders** | Mixed: `kapstongImage` (no separator, camelCase-ish), `student_functions` (snake_case) | **`kebab-case`** or **`snake_case`** consistently — recommend `snake_case` to match PHP file convention |
| **CSS/JS files** | Consistently camelCase (`loginPhase.css`, `adminDashboard.js`) — this pairing is actually fine since they mirror their PHP counterpart names | Keep as-is for consistency with their PHP pair, OR migrate both together to `kebab-case` if you want full consistency. Either is defensible; just be consistent. |
| **Database tables** (unknown — not in tree) | N/A | Once you share schema: use `snake_case`, plural nouns (`students`, `attendance_logs`), no Hungarian-style prefixes (no `tbl_students`) |
| **Database columns** | N/A | `snake_case`, singular, descriptive (`created_at` not `dt`, `student_id` not `sid`) |
| **Functions (inside PHP files)** | Unknown without source | `camelCase()` for functions is the PHP-community norm (PSR doesn't mandate, but it's near-universal) |
| **Classes** (if you introduce any in refactor) | None currently — fully procedural | `PascalCase` per PSR-1 |
| **Constants** | Unknown | `UPPER_SNAKE_CASE` |
| **Routes/URLs** (if you add a router) | Currently = filenames | `kebab-case` URLs (`/student/submit-task` not `/student/submitTask.php`) |

**The single biggest naming win available to you:** resolve the `registerRFID.php` / `register_rfid.php` duplicate-naming collision (finding #2). That one pair, sitting in the same folder with two different casing conventions, is the most visible inconsistency in the whole tree.

---

## 6. Security Audit (Risk Checklist — Verify Against Actual Code)

I want to be precise about what this section is: based on filenames alone, I can tell you *where to look*, not *what's broken*. Treat each row as a thing to go check, not a confirmed finding.

| Area | Why It's Flagged | What to Check | Severity if Present |
|---|---|---|---|
| **Hardcoded DB credentials** | `kapstongConnection.php` sits as a loose file with no visible `config/` or `.env` pattern in the tree | Open the file — is `mysqli_connect("localhost", "root", "password", "db")` written directly in it? | Critical |
| **SQL Injection** | 60+ files named `search*.php` (`searchStudent.php`, `searchAllStudent.php`, `searchAssignTaskStudent.php`, etc.) and `get*.php` strongly suggest dynamic queries built from user input (search boxes, filters) | Are these using **prepared statements** (`mysqli::prepare` / PDO bound params), or string-concatenated SQL like `"SELECT * FROM students WHERE name LIKE '%$input%'"`? | Critical |
| **Broken access control / missing auth checks** | Dozens of files directly under `admin/functions/`, `supervisor/functions/`, `student/student_functions/` — if each is independently web-accessible, every single one needs its own auth check | Does every file in these folders `require` or `include` an auth guard (`admin_auth.php`, etc.) **at the very top**, before any logic runs? Spot-check several files, not just one. | Critical |
| **RFID authentication bypass** | `rfid_login.php`, `rfid_test.php` (a *test* file sitting next to the real login logic) | Is `rfid_test.php` still deployed/accessible? Does RFID login validate the card ID server-side against the DB, or trust client-submitted data? | Critical |
| **File upload vulnerabilities** | `profile_upload.php`, `upload_documents_action.php` | Do these validate file **type by content** (not just extension), enforce a size limit, rename uploaded files (avoid path traversal via filename), and store outside the web-executable root? | Critical |
| **Email/header injection** | `mailer.php`, `sendEmail.php`, `sendResetOTP.php` use PHPMailer (good choice) but the wrapping logic matters | Is user input (e.g. signup email, name) sanitized before being passed into PHPMailer's `setFrom`/`addAddress`/subject fields? | High |
| **OTP brute-forcing** | `verifyOTP.php`, `verifyResetOTP.php`, `resendOTP.php`, `resendResetOTP.php` | Is there rate-limiting on OTP attempts? Is the OTP itself sufficiently long/random (not a predictable 4-digit sequence with no attempt cap)? | High |
| **Session security** | `sessionTimeOut.php`, `loginPhase.php` | Are session cookies set with `HttpOnly`, `Secure`, and `SameSite` flags? Is `session_regenerate_id()` called on login to prevent session fixation? | High |
| **CSRF protection** | No `csrf` or `token` related filenames visible anywhere in the tree | Almost certainly **no CSRF tokens** on state-changing forms (approve student, assign task, update settings, etc.) — this is a real gap to close, not just a hypothetical. | High |
| **PDF/report export path traversal or data leakage** | `download_attendance_report.php`, `download_all_students_pdf.php`, `download_line_attendance.php`, `exportFinalEvaluation.php` | Do these check that the requesting user (e.g. a supervisor) is actually authorized to download *that specific* student's/line's data, or just that *some* session exists? | High |
| **XSS** | Any place user input (student name, task description, report text) is echoed back into HTML | Is output passed through `htmlspecialchars()` before rendering, especially in admin tables (`renderAssignStudentList.php`) and search results? | Medium–High |
| **Exposed test/debug endpoints** | `testpdf.php`, `sendEmailTest.php`, `rfid_test.php` | These should not exist in a deployed copy at all — confirmed structural issue (see finding #4), also a security issue if reachable. | High |
| **Emergency/override functions** | `emergency_timeout.php` | This name implies a function that can force-close attendance sessions — make sure this is admin-only and logged (audit trail), since it's an obvious target for misuse. | Medium |

---

## 7. GitHub Readiness Review

**Current state:** not ready for public GitHub as-is. The fixes are mostly mechanical, though, not architectural.

### Files to add and their purpose

| File | Purpose |
|---|---|
| `README.md` | First thing anyone sees. Should include: what the project is (OJT attendance/task tracker for 3 roles), tech stack, screenshots, setup instructions (link to `docs/INSTALLATION.md`), and a note that this is a student/academic project. |
| `LICENSE` | Without one, technically nobody else has legal permission to use/fork your code even though it's public. For a portfolio piece, MIT License is the simplest, most permissive, most commonly expected choice. |
| `.gitignore` | See §8 — critical, must be added before your *next* commit if not already present. |
| `.env.example` | Template showing *which* environment variables are needed (`DB_HOST=`, `DB_USER=`, `MAIL_PASSWORD=`, etc.) without real values — lets a new dev know what to fill in. |
| `CONTRIBUTING.md` | Optional for a solo student project, but a short one signals professionalism — even just "this is a personal/academic project, not currently accepting external contributions" is fine. |
| `CHANGELOG.md` | Optional; nice to have if you want to track version history for your portfolio narrative, but not essential for OJT applications. |
| `CODE_OF_CONDUCT.md` | Skip for now — genuinely unnecessary for a solo academic project with no contributors. Don't pad the repo with template files you don't need; reviewers notice empty ceremony. |
| `SECURITY.md` | Worth adding briefly once you've actually fixed the items in §6 — a short note like "this is an academic project; if you find a security issue, open an issue or contact me directly" is enough. |

---

## 8. .gitignore — What Must Never Be Committed

```gitignore
# Environment & secrets
.env
.env.local
config/database.php
config/mail.php

# Dependencies
/vendor/
/node_modules/

# Uploaded user content
/storage/uploads/*
!/storage/uploads/.gitkeep

# Logs & cache
/storage/logs/*
!/storage/logs/.gitkeep
/storage/cache/*

# IDE / OS files
.vscode/
.idea/
.DS_Store
Thumbs.db

# Composer
composer.phar

# Backups / dumps
*.sql
*.sql.gz
*.bak

# Misc debug/test artifacts found in current tree
exit
testpdf.php
sendEmailTest.php
rfid_test.php
```

**Why each category matters:**

- **`.env` / `config/database.php`** — contains your real DB password and SMTP credentials. If committed even once, it's in git history forever (a `.gitignore` added *after* the fact doesn't remove it from old commits — you'd need `git filter-repo` or BFG to scrub it).
- **`/vendor/`** — this is your `composer.json`-managed dependency tree (PHPMailer, phpqrcode, thecodingmachine/safe). It's huge, regenerable from `composer install`, and committing it bloats the repo and creates merge-conflict risk on dependency updates.
- **`/storage/uploads/`** — real students' uploaded documents/photos. Committing these is both a privacy issue (these are presumably real classmates' data) and pointless bloat.
- **`*.sql` dumps** — a full database export often contains real student records, hashed (or worse, plaintext) passwords, and personal info. Never commit these even for "demo data" purposes — use a sanitized seed script instead.
- **`exit`, `testpdf.php`, `sendEmailTest.php`, `rfid_test.php`** — specific to your current tree; these are debug/test artifacts that shouldn't ship.

---

## 9. Keep Local Only

Beyond the `.gitignore` mechanics above, here's the explicit list of *categories* of things that should never reach GitHub, with the actual risk spelled out:

| Item | Risk if Committed |
|---|---|
| Real database credentials (in `kapstongConnection.php` or `.env`) | Anyone on the internet could connect directly to your database, read/modify/delete all student, supervisor, and admin records. |
| SMTP/email credentials (in `mailer.php` config) | Your email account could be hijacked to send spam/phishing — your account, not an abstract "the system's." |
| Real student personal data (uploaded documents, photos in `kapstongImage/`, any SQL dump) | Privacy violation against real classmates whose data is in this system — this is the most ethically serious item on this list. |
| Session secret / app encryption keys (if you add any during refactor) | Allows forging valid session tokens, fully bypassing login. |
| Any `id_rsa` or similar private key (note: I confirmed the one visible in your tree is inside the third-party `safe` package's own test fixtures, not yours — but the same rule applies if you ever generate real SSH/deploy keys for this project) | A private key in a public repo can be used to impersonate you/your server immediately, and rotating it after exposure doesn't undo the exposure window. |
| RFID card UID-to-student mapping table dumps | Could let someone clone or spoof RFID access. |

---

## 10. Quick Take: Top 5 Highest-Leverage Fixes

Given you want this for portfolio/resume purposes specifically, here's what to prioritize if you only do five things:

1. **Move `kapstongConnection.php` credentials into `.env`** — single highest security ROI, takes under an hour.
2. **Delete/quarantine test files** (`rfid_test.php`, `testpdf.php`, `sendEmailTest.php`) and the duplicate RFID register file — instant cleanliness win, no logic risk.
3. **Add `.gitignore` + `README.md` + `LICENSE`** before your next push — makes the repo link itself resume-ready in an afternoon.
4. **Verify auth guards exist on every file in the `functions/` folders** — the single biggest *real* security question mark, worth an afternoon of grep-and-check (`grep -L "auth_guard" php/admin/functions/*.php` to find files missing the include).
5. **Write `docs/ARCHITECTURE.md`** describing the role-based modular structure — this single document is what you'd actually walk an interviewer through, and writing it will also surface any structural confusion you hadn't noticed yet.

---

*If you'd like, I can go deeper on any one section above (e.g. a full prepared-statement rewrite example, a complete README draft, or the actual `auth_guard.php` pattern to enforce consistently) — just point me at the specific file content and I'll work from the real code instead of inference.*

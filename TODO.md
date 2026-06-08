# Islamic Scholar - Issues & Remaining Work

## Critical Bugs

- [x] **Missing DB tables** — `admin_users`, `enrollments`, and `orders` tables now created in `config.php`
- [x] **Path casing bug** — already fixed, both paths use lowercase `islamic_scholar`
- [x] **Race condition** — fixed: now uses `lastInsertId()` after INSERT, no collision possible

## Security Issues

- [x] **Unsafe file uploads** — added extension whitelist, MIME type validation, file size limits, and random filenames
- [x] **No CSRF tokens** — added CSRF tokens to all forms and server-side validation to `order_submit.php`
- [x] **XSS in view_book.php** — already fixed, uses `htmlspecialchars()` and no JS context rendering
- [x] **DOM-based XSS in audios.php** — fixed: error messages no longer expose file paths, uses `textContent`
- [x] **GET-based admin logout** — changed to POST form with CSRF token
- [x] **No session security** — added `httponly`, `samesite=Strict`, `use_only_cookies`, 1-hour timeout
- [x] **Hardcoded credentials in HTML** — removed from `admin/login.php`
- [x] **No account lockout** — added 5-attempt lockout with 15-min cooldown on both login pages
- [x] **No rate limiting** — added session-based rate limiting to fatwa and order submissions

## Input Validation

- [x] **No validation on admin forms** — added sanitization and required field checks to courses, articles, books, audios
- [x] **No password strength rules** — `register.php` now requires 8+ chars with letters and numbers
- [x] **No email format validation** — `order_submit.php` now validates email and phone format
- [x] **Error messages expose internals** — replaced with generic messages, errors logged via `error_log()`

## Non-Functional Features

- [ ] **Enrollment system** — "Enroll Free" button in `courses.php` only shows a JS alert, no backend logic
- [ ] **Fatwa search** — search box in `fatwa.php` has no backend implementation
- [ ] **Email sending** — `fatwa_submit.php` has email commented out, never sends
- [ ] **Donation buttons** — non-functional, no payment integration
- [x] **User dashboard** — full profile & dashboard at `user_dashboard.php` with stats, enrollments, and password change
- [ ] **Password reset** — no forgot password functionality
- [ ] **Email verification** — no confirmation email on registration

## Code Quality

- [x] **No `.htaccess`** — added root `.htaccess` (security headers, block sensitive files) + `uploads/.htaccess` (block PHP execution)
- [ ] **No HTTPS enforcement** — `SITE_URL` uses `http://` (fine for local dev)
- [x] **Inconsistent sanitization** — admin forms now use `sanitize()` and cast IDs to `(int)`
- [ ] **No logging** — no record of errors, failed logins, or form submissions
- [x] **No admin user management** — can create, delete admin accounts and change passwords at `admin/users.php`

---

*Last updated: 2026-05-01*

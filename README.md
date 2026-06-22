# Books API — Chapter 12 Secure Version

This folder contains the Chapter 12 version of the SCSM2223 Books API.
It includes the Chapter 11 JWT backend, the Vue frontend, and Chapter 12 security hardening.

## Included features

- PHP Slim 4 REST API
- PDO + MySQL database
- JWT register/login/authentication
- Vue 3 + Vite frontend UI
- Validator helper for strict input validation
- XSS-safe JSON encoding
- Security HTTP headers
- Rate limiting on `/auth/login`
- CORS allow-list
- `created_by` owner column and IDOR protection on update
- `audit_log` table and audit records

## Run backend

```bat
cd C:\laragon\www\books-api-secure
composer install
composer dump-autoload
mysql -u root < sql/schema.sql
php -S localhost:8000 -t public
```

Backend URL:

```text
http://localhost:8000
```

## Run frontend

Open a second terminal:

```bat
cd C:\laragon\www\books-api-secure\frontend
npm install
npm run dev
```

Frontend URL:

```text
http://localhost:5173
```

## Demo users

```text
Admin:  admin@books.test  / password
Member: member@books.test / password
```

Admin can delete books. Member can create books and update only books they own.

## If login says invalid password

Run this to reset demo passwords to `password`:

```bat
mysql -u root -e "USE books_api; UPDATE users SET password_hash='$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi' WHERE email IN ('admin@books.test','member@books.test');"
```

## If PDO says could not find driver

Open `Fix_PDO_MySQL_Driver.md` and enable `pdo_mysql` in the same `php.ini` used by Laragon Terminal.

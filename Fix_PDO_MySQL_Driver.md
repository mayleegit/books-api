# Fix: "PDOException: could not find driver"

**Course:** SCSM2223 — Cross-Platform Application Development
**Applies to:** Chapter 10 (PDO + MySQL) and any later chapter that uses the database.

## What this error means

When you start the API and immediately get something like:

```
[DB] connection failed: could not find driver
PHP Fatal error:  Uncaught PDOException: could not find driver in C:\laragon\www\Chapter10\src\Database.php:41
```

it is **not** a bug in the code — it is a PHP configuration issue. The PHP process you are using to run the dev server does not have the **`pdo_mysql`** extension loaded, so PDO has no driver available for the `mysql:` DSN.

This typically happens when:

- Laragon ships several PHP versions and your terminal is using a different one than you expected.
- `pdo_mysql` is commented out in that PHP's `php.ini`.
- `extension_dir` in `php.ini` points to the wrong folder.

The fix is to make sure the **same** PHP that runs your server has `pdo_mysql` enabled.

## Step 1 — Find out which php.ini is in effect

Open the **Laragon Terminal** (so PATH is already set up) and run:

```
php --ini
```

Look at the line that starts with:

```
Loaded Configuration File: ...
```

That is the `php.ini` file you must edit. On a typical Laragon install it looks something like:

```
C:\laragon\bin\php\php-8.x.x-Win32-VS16-x64\php.ini
```

Then check whether `pdo_mysql` is currently loaded:

```
php -m | findstr /i pdo
```

If you only see `PDO` (without `pdo_mysql`), the extension is missing — that confirms the cause.

## Step 2 — Enable pdo_mysql in php.ini

Open the `php.ini` file from Step 1 in any text editor (Notepad++, VS Code, Sublime Text). Search for these two lines:

```
;extension=pdo_mysql
;extension=mysqli
```

Remove the leading semicolons so they read:

```
extension=pdo_mysql
extension=mysqli
```

While you are there, check that `extension_dir` points to the `ext` folder of **this same** PHP install. Either of these is fine:

```
extension_dir = "ext"
```

or the absolute path matching your PHP folder, e.g.:

```
extension_dir = "C:\laragon\bin\php\php-8.x.x-Win32-VS16-x64\ext"
```

Save the file.

## Step 3 — Restart everything

The PHP built-in server reads `php.ini` only at startup. Restart it:

1. Stop the running server with **Ctrl+C** in the terminal.
2. Start it again:

   ```
   cd C:\laragon\www\Ch10_BooksAPI_Solution
   php -S localhost:8000 -t public
   ```

If you are running through Laragon's Apache, click **Reload** in Laragon (or **Stop All** then **Start All**).

## Step 4 — Verify the fix

Back in the terminal:

```
php -m | findstr /i pdo
```

You should now see both:

```
PDO
pdo_mysql
```

Then hit the API:

```
curl http://localhost:8000/api/books
```

You should get a JSON response with the seeded books. The `could not find driver` error is gone.

## Multiple PHP versions installed?

This is the single most common cause. Laragon often has several PHP versions side-by-side in `C:\laragon\bin\php\`. Your CLI uses whichever one is on PATH, which may be **different** from the one Laragon's Apache uses or different from the one whose `php.ini` you edited.

Find which `php.exe` your terminal is actually running:

```
where php
```

The first path shown is the one in use. Make sure that is the PHP install whose `php.ini` you just edited. If it is not:

- In Laragon: **Menu → PHP → Version** and pick the version you want.
- Or add the correct PHP folder to the front of your Windows PATH.

Then **open a new terminal** so the change takes effect, and repeat Step 4.

## Quick sanity check before every database lab

Before running `php -S localhost:8000 -t public`, run this one-liner:

```
php -v && php -m | findstr /i pdo_mysql
```

You should see your PHP version followed by `pdo_mysql`. If `pdo_mysql` does not print, fix it before starting the server — you will save yourself a debugging session.

## Related troubleshooting

| Symptom                                              | Likely cause                            | Fix                                                                              |
| ---------------------------------------------------- | --------------------------------------- | -------------------------------------------------------------------------------- |
| `PDOException: could not find driver`                | `pdo_mysql` not enabled                 | Steps 2–3 above.                                                                 |
| `SQLSTATE[HY000] [2002] No connection`               | MySQL service not running               | Start Laragon → click **Start All** (green button).                              |
| `SQLSTATE[28000] Access denied for user`             | Wrong `DB_USER` / `DB_PASS` in `.env`   | Open `.env` and match Laragon's MySQL credentials (default `root` / empty pass). |
| `SQLSTATE[42S02] Base table 'books_api.books' not found` | Schema script not loaded            | Run `sql/schema.sql` in HeidiSQL, then refresh.                                  |
| `SQLSTATE[HY093] Invalid parameter number`           | Reused the same named placeholder twice | Give each `:name` a unique name (e.g. `:q_title`, `:q_author`) and bind both.    |

---

*Save this file for reference — students hit the `could not find driver` error almost every semester on a fresh Laragon install, especially when switching PHP versions.*

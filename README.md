# UTM Books

A full-stack book management system built with Vue 3, PHP Slim Framework, MySQL, JWT Authentication, and Capacitor Android.

## Features

### Authentication & Security

* User registration
* User login with JWT authentication
* Protected API endpoints
* Role-based access control (Admin / Member)
* Rate limiting on login attempts
* Security headers
* Audit logging

### Book Management

* View all books
* Search books by title or author
* Create new books
* Edit owned books
* Delete books (Admin only)

### User Profile

* View account information
* View user role
* View registration date

### Deployment

* Frontend deployed on Vercel
* Backend deployed on Railway
* MySQL database hosted on Railway

### Mobile Support

* Capacitor Android integration
* Android Studio deployment support

---

## Tech Stack

### Frontend

* Vue 3
* Vue Router
* Pinia
* Axios
* Vite

### Backend

* PHP 8
* Slim Framework 4
* Firebase PHP JWT

### Database

* MySQL

### Deployment

* Vercel
* Railway

---

## Demo Accounts

### Admin

Email:
[admin@books.test](mailto:admin@books.test)

Password:
password

### Member

Email:
[member@books.test](mailto:member@books.test)

Password:
password

---

## Local Setup

### Backend

```bash
composer install
php -S localhost:8000 -t public
```

### Frontend

```bash
cd frontend
npm install
npm run dev
```

---

## Production URLs

Frontend:
https://utm-books-murex.vercel.app

Backend:
https://books-api-production-3a35.up.railway.app

---

## Android Build

```bash
cd frontend

npm install
npm run build

npx cap sync android
npx cap open android
```

Run the project using Android Studio emulator or a physical Android device.

---

## Project Structure

```text
books-api/
│
├── public/
├── src/
├── sql/
├── frontend/
│   ├── src/
│   ├── android/
│   └── capacitor.config.json
│
├── composer.json
└── README.md
```

---

## Author

May Yan

Universiti Teknologi Malaysia (UTM)

Chapter 12 & Chapter 13 Lab Project

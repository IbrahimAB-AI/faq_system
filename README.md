# faqsystem
frequently asked questions for junior/beginner developers system 


# FAQ System

A web-based FAQ system for programming beginners with an integrated AI assistant.

## Features

### User Features
- 🔍 Search FAQs by keyword
- 📁 Browse by categories
- 📖 View detailed FAQ answers
- 👤 User registration and login
- ❓ Submit questions for review
- 💬 Chat with AI assistant

### Admin Features
- 📊 Dashboard with statistics
- 📝 Manage FAQs (CRUD)
- 📂 Manage categories
- ✅ Review submitted questions
- 📈 View search analytics

## Tech Stack

- **Backend:** PHP 8
- **Database:** MySQL
- **Frontend:** HTML5, CSS3, JavaScript
- **UI Framework:** Bootstrap 5
- **AI:** Groq API (LLaMA 3.1)

## Installation

### 1. Database Setup
```sql
-- Create database and import
mysql -u root -p < database/faq_system.sql
define('DB_HOST', 'localhost');
define('DB_NAME', 'faq_system');
define('DB_USER', 'root');
define('DB_PASS', 'your_password');

define('GROQ_API_KEY', 'your_groq_api_key');

| Role  | Email               | Password    |
| ----- | ------------------- | ----------- |
| Admin | admin@faqsystem.com | password123 |
| User  | john@example.com    | password123 |


faq_system/
├── config/          # Database & API config
├── includes/        # Reusable PHP includes
├── database/        # SQL schema
├── public/          # Public pages
├── admin/           # Admin pages
└── assets/          # CSS & JS files



---

## ✅ All Files Complete!

### Summary of 26 files created:

**Config (2):** db.php, groq.php  
**Database (1):** faq_system.sql  
**Includes (4):** header.php, footer.php, auth.php, functions.php  
**Public (9):** index, search, category, faq, login, register, logout, submit_question, agent  
**Admin (5):** index, manage_faqs, manage_categories, review_submissions, search_logs  
**Assets (4):** style.css, agent.css, main.js, agent.js  
**Docs (1):** README.md

---

Want me to help you push everything to GitHub now?
-- FAQ System Database Schema
## database/faq_system.sql (Full)

```sql
-- =====================================================
-- FAQ System Database Schema
-- Web-Based FAQ System for Programming Beginners
-- =====================================================

-- -----------------------------------------------------
-- USERS TABLE
-- -----------------------------------------------------
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role)
);

-- -----------------------------------------------------
-- CATEGORIES TABLE
-- -----------------------------------------------------
CREATE TABLE categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_category_name (category_name)
);

-- -----------------------------------------------------
-- FAQs TABLE
-- -----------------------------------------------------
CREATE TABLE faqs (
    faq_id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    question VARCHAR(500) NOT NULL,
    answer LONGTEXT NOT NULL,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_category (category_id),
    INDEX idx_created_by (created_by)
);

-- -----------------------------------------------------
-- SEARCH LOGS TABLE
-- -----------------------------------------------------
CREATE TABLE search_logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    search_query VARCHAR(255) NOT NULL,
    searched_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    user_id INT,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL,
    INDEX idx_search_query (search_query),
    INDEX idx_searched_at (searched_at),
    INDEX idx_user (user_id)
);

-- -----------------------------------------------------
-- SUBMITTED QUESTIONS TABLE
-- -----------------------------------------------------
CREATE TABLE submitted_questions (
    submission_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    question VARCHAR(500) NOT NULL,
    category_id INT,
    status ENUM('pending', 'approved', 'dismissed') DEFAULT 'pending',
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    reviewed_by INT,
    reviewed_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE SET NULL,
    FOREIGN KEY (reviewed_by) REFERENCES users(user_id) ON SET NULL,
    INDEX idx_user (user_id),
    INDEX idx_status (status)
);

-- -----------------------------------------------------
-- AGENT SESSIONS TABLE
-- -----------------------------------------------------
CREATE TABLE agent_sessions (
    session_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    session_token VARCHAR(64) UNIQUE NOT NULL,
    messages LONGTEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL,
    INDEX idx_session_token (session_token),
    INDEX idx_user (user_id),
    INDEX idx_updated_at (updated_at)
);

-- -----------------------------------------------------
-- NOTIFICATIONS TABLE
-- -----------------------------------------------------
CREATE TABLE notifications (
    notification_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_is_read (is_read)
);

-- =====================================================
-- SEED DATA (Testing)
-- =====================================================

-- Password for all accounts: password123
-- Hash: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi

INSERT INTO users (username, email, password_hash, role) VALUES 
('admin', 'admin@faqsystem.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

INSERT INTO users (username, email, password_hash, role) VALUES 
('johndoe', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user');

INSERT INTO categories (category_name, description) VALUES 
('HTML Basics', 'Fundamental HTML concepts for beginners'),
('CSS Styling', 'CSS fundamentals and styling techniques'),
('JavaScript', 'JavaScript programming basics'),
('PHP', 'Server-side PHP programming'),
('Database', 'MySQL and database concepts'),
('Git Version Control', 'Git and GitHub basics'),
('Web Security', 'Basic web security practices'),
('Responsive Design', 'Making websites mobile-friendly');

INSERT INTO faqs (category_id, question, answer, created_by) VALUES 
(1, 'What is HTML?', 'HTML stands for HyperText Markup Language. It is the standard markup language for creating web pages.', 1),
(1, 'What is the difference between div and span?', 'The <div> element is a block-level element used for grouping. The <span> is an inline element used for small portions of text.', 1),
(1, 'What are semantic HTML elements?', 'Semantic elements clearly describe their meaning. Examples: <header>, <nav>, <article>, <section>, <footer>.', 1),
(2, 'How do I center a div in CSS?', 'Use Flexbox: display: flex; justify-content: center; align-items: center;', 1),
(2, 'What is the box model in CSS?', 'Content, padding, border, and margin. Use box-sizing: border-box to include padding in width.', 1),
(2, 'How do I make a responsive website?', 'Use media queries (@media), flexible units (%, rem), and CSS Grid or Flexbox.', 1),
(3, 'What is the difference between let and const?', 'let allows reassignment; const does not. Use const by default for values that wont change.', 1),
(3, 'How do I select an element in JavaScript?', 'document.getElementById("id"), document.querySelector(".class"), querySelectorAll()', 1),
(3, 'What is DOM manipulation?', 'Creating, modifying, or deleting HTML elements using JavaScript.', 1),
(4, 'What is the difference between echo and print?', 'echo is faster, print returns 1. Both output data to the screen.', 1),
(4, 'How do I connect PHP to MySQL?', 'Use PDO: $pdo = new PDO("mysql:host=localhost;dbname=database", "user", "pass");', 1),
(4, 'What is a PHP session?', 'Sessions store user data across pages. Use session_start(), $_SESSION["key"] = "value".', 1),
(5, 'What is a primary key?', 'A unique identifier for each record in a table. Cannot be NULL.', 1),
(5, 'What is a foreign key?', 'A field that references the primary key in another table.', 1),
(5, 'What is SQL injection?', 'Malicious code in SQL queries. Prevent with prepared statements.', 1),
(6, 'What is Git?', 'Distributed version control system for tracking code changes.', 1),
(6, 'How do I create a new branch in Git?', 'git branch branch-name or git checkout -b branch-name', 1),
(6, 'What is a pull request?', 'Propose changes to merge into main branch.', 1),
(7, 'What is XSS?', 'Cross-Site Scripting. Prevent by sanitizing input and escaping output.', 1),
(7, 'What is CSRF?', 'Cross-Site Request Forgery. Prevent with CSRF tokens.', 1),
(7, 'How do I secure password storage?', 'Use password_hash() and password_verify(). Never store plain text.', 1),
(8, 'What is a viewport meta tag?', '<meta name="viewport" content="width=device-width, initial-scale=1">', 1),
(8, 'What are CSS breakpoints?', 'Points where layout changes. Use @media queries for different screen sizes.', 1),
(8, 'What is mobile-first design?', 'Design for mobile first, then enhance for larger screens.', 1);

INSERT INTO notifications (user_id, message, is_read) VALUES 
(1, 'Welcome to FAQ System! Start exploring programming questions.', 0);
```

---

Copy this into your `database/faq_system.sql` file. Then import it in phpMyAdmin to create all tables with sample data!

**Default login:** `admin@faqsystem.com` / `password123`
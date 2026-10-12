CREATE DATABASE IF NOT EXISTS smart_library;
USE smart_library;

DROP TABLE IF EXISTS borrowings;
DROP TABLE IF EXISTS books;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    student_id VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('student','librarian','admin') NOT NULL DEFAULT 'student',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE books (
    book_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    author VARCHAR(150) NOT NULL,
    isbn VARCHAR(50),
    category VARCHAR(100),
    description TEXT,
    quantity INT NOT NULL DEFAULT 1,
    available_quantity INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE borrowings (
    borrowing_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    borrow_date DATE NOT NULL,
    due_date DATE NOT NULL,
    return_date DATE NULL,
    status ENUM('Borrowed','Returned') NOT NULL DEFAULT 'Borrowed',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_borrow_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    CONSTRAINT fk_borrow_book FOREIGN KEY (book_id) REFERENCES books(book_id) ON DELETE CASCADE
);

-- Demo student account:
-- Student ID: ST001
-- Password: password
INSERT INTO users (name, email, student_id, password, role)
VALUES ('Demo Student', 'student@example.com', 'ST001',
'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC5zFf3Y6F5u6F3G6f6K', 'student');

INSERT INTO books (title, author, isbn, category, description, quantity, available_quantity) VALUES
('Database Systems', 'Thomas Connolly', '9780132943260', 'Database', 'Introduction to database concepts and systems.', 3, 3),
('Java Programming', 'Herbert Schildt', '9781260440210', 'Programming', 'A practical guide to Java programming.', 2, 1),
('Web Development', 'Jon Duckett', '9781118871652', 'Web Development', 'HTML, CSS and JavaScript fundamentals.', 4, 4),
('Software Engineering', 'Ian Sommerville', '9780133943030', 'Software Engineering', 'Principles and practices of software engineering.', 2, 2),
('Computer Networks', 'Andrew S. Tanenbaum', '9780132126953', 'Networking', 'Foundations of computer networking.', 1, 0),
('Clean Code', 'Robert C. Martin', '9780132350884', 'Programming', 'A practical guide to writing readable, maintainable software.', 3, 3),
('The Pragmatic Programmer', 'David Thomas and Andrew Hunt', '9780135957059', 'Programming', 'Timeless lessons for becoming a thoughtful and effective programmer.', 2, 2),
('Introduction to Algorithms', 'Thomas H. Cormen', '9780262046305', 'Algorithms', 'A broad introduction to modern algorithms and data structures.', 2, 2),
('Designing Data-Intensive Applications', 'Martin Kleppmann', '9781449373320', 'Databases', 'Reliable, scalable, and maintainable approaches to data systems.', 2, 2);

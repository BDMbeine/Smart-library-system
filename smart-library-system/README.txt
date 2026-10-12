SMART LIBRARY MANAGEMENT SYSTEM
Daniel's Prototype - Registration + Borrowing Increment

TECHNOLOGY
- PHP
- MySQL
- HTML/CSS
- XAMPP

FEATURES IN THIS VERSION
1. Student login
2. Student registration
3. Book search
4. Book details and availability
5. Borrow a book
6. Automatic availability update after borrowing
7. 14-day due date
8. My Books page showing borrowing history
9. Overdue status display
10. Logout

SETUP WITH XAMPP
1. Install XAMPP.
2. Copy this folder into:
   C:\xampp\htdocs\
3. Start Apache and MySQL from the XAMPP Control Panel.
4. Open:
   http://localhost/phpmyadmin/
5. Create/import the database by importing:
   database/smart_library.sql
6. Open:
   http://localhost/smart-library-system/

DEMO LOGIN
Student ID: ST001
Password: password

TEST THE NEW REGISTRATION FEATURE
1. Open the login page.
2. Click "Register as a student".
3. Enter a new name, email, student ID and password.
4. Submit the form.
5. Log in with the new account.

TEST THE BORROWING FEATURE
1. Log in.
2. Click "Search Books".
3. Open a book that has available copies.
4. Click "Borrow Book".
5. The available quantity decreases by one.
6. Open "My Books".
7. The borrowed book appears with a 14-day due date.

PRESENTATION DEMO FLOW
Registration -> Login -> Search Book -> View Availability -> Borrow Book -> My Books

IMPORTANT
This is a prototype/product increment for the Scrum assignment. It is not intended as a production-ready university library system. Librarian return processing, fines, notifications and full administration can be future backlog items.

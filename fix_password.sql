UPDATE users SET password = '$2y$10$Vlu7BqWCYZyfdXput7/AIe5j7lZgjCuQ.v4tL/PXSTxM6eo9K5tdu' WHERE username IN ('admin', 'teacher1', 'teacher2', 'student1', 'student2', 'student3', 'student4');
SELECT username, PASSWORD(password) FROM users LIMIT 1;

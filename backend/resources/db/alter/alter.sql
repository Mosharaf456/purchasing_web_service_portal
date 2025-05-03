SET GLOBAL validate_password.policy = LOW;
SET GLOBAL validate_password.length = 4;
SET GLOBAL validate_password.number_count = 0;
SET GLOBAL validate_password.mixed_case_count = 0;
SET GLOBAL validate_password.special_char_count = 0;
SET GLOBAL validate_password.dictionary_file = '';
SET GLOBAL validate_password.check_user_name = OFF;
CREATE DATABASE testdb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE USER 'testuser'@'localhost' IDENTIFIED BY '123456';
GRANT ALL PRIVILEGES ON testdb.* TO 'testuser'@'localhost';
FLUSH PRIVILEGES;

CREATE TABLE testdb.test_table (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


-- v1.0.0 --- 
DROP TABLE migrations;
DROP TABLE password_reset_tokens;
DROP TABLE sessions;



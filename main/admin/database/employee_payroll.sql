CREATE DATABASE IF NOT EXISTS employee_payroll;
USE employee_payroll;

CREATE TABLE admins(
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(100) NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE departments(
    department_id INT AUTO_INCREMENT PRIMARY KEY,
    department_name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE positions(
    position_id INT AUTO_INCREMENT PRIMARY KEY,
    position_name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE employees(

employee_id INT AUTO_INCREMENT PRIMARY KEY,

employee_number VARCHAR(20) UNIQUE,

firstname VARCHAR(100) NOT NULL,

lastname VARCHAR(100) NOT NULL,

gender ENUM('Male','Female') NOT NULL,

email VARCHAR(150) UNIQUE NOT NULL,

phone VARCHAR(20),

department_id INT,

position_id INT,

salary DECIMAL(10,2) DEFAULT 0,

username VARCHAR(50) UNIQUE,

password VARCHAR(255),

photo VARCHAR(255) DEFAULT 'default.png',

status ENUM('Active','Inactive') DEFAULT 'Active',

date_employed DATE,

created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

FOREIGN KEY(department_id)
REFERENCES departments(department_id)
ON UPDATE CASCADE
ON DELETE SET NULL,

FOREIGN KEY(position_id)
REFERENCES positions(position_id)
ON UPDATE CASCADE
ON DELETE SET NULL

);


CREATE TABLE leave_requests(

leave_id INT AUTO_INCREMENT PRIMARY KEY,

employee_id INT NOT NULL,

leave_type ENUM(
'Annual',
'Sick',
'Maternity',
'Paternity',
'Study',
'Compassionate'
),

reason TEXT,

start_date DATE,

end_date DATE,

status ENUM(
'Pending',
'Approved',
'Rejected'
) DEFAULT 'Pending',

applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

FOREIGN KEY(employee_id)
REFERENCES employees(employee_id)
ON DELETE CASCADE

);


CREATE TABLE payroll(

payroll_id INT AUTO_INCREMENT PRIMARY KEY,

employee_id INT,

basic_salary DECIMAL(10,2),

housing DECIMAL(10,2),

transport DECIMAL(10,2),

medical DECIMAL(10,2),

bonus DECIMAL(10,2),

tax DECIMAL(10,2),

pension DECIMAL(10,2),

other_deductions DECIMAL(10,2),

net_salary DECIMAL(10,2),

pay_month VARCHAR(20),

pay_year YEAR,

created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

FOREIGN KEY(employee_id)
REFERENCES employees(employee_id)
ON DELETE CASCADE

);

CREATE TABLE payslips(

payslip_id INT AUTO_INCREMENT PRIMARY KEY,

payroll_id INT,

employee_id INT,

generated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

FOREIGN KEY(payroll_id)
REFERENCES payroll(payroll_id)
ON DELETE CASCADE,

FOREIGN KEY(employee_id)
REFERENCES employees(employee_id)
ON DELETE CASCADE

);

INSERT INTO departments(department_name,description) VALUES

('Human Resources','HR Department'),

('Information Technology','IT Department'),

('Finance','Finance Department'),

('Marketing','Marketing Department'),

('Administration','Administration Department');

INSERT INTO positions(position_name,description) VALUES

('Manager','Department Manager'),

('Software Engineer','Software Developer'),

('Accountant','Finance Officer'),

('Secretary','Office Secretary'),

('HR Officer','Human Resource Officer');

CREATE TABLE allowances(
    allowance_id INT AUTO_INCREMENT PRIMARY KEY,
    allowance_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE employee_allowances(
    employee_allowance_id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    allowance_id INT NOT NULL,
    amount DECIMAL(10,2) DEFAULT 0,

    FOREIGN KEY(employee_id)
    REFERENCES employees(employee_id)
    ON DELETE CASCADE,

    FOREIGN KEY(allowance_id)
    REFERENCES allowances(allowance_id)
    ON DELETE CASCADE
);

CREATE TABLE deductions(
    deduction_id INT AUTO_INCREMENT PRIMARY KEY,
    deduction_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE employee_deductions(
    employee_deduction_id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    deduction_id INT NOT NULL,
    amount DECIMAL(10,2) DEFAULT 0,

    FOREIGN KEY(employee_id)
    REFERENCES employees(employee_id)
    ON DELETE CASCADE,

    FOREIGN KEY(deduction_id)
    REFERENCES deductions(deduction_id)
    ON DELETE CASCADE
);

INSERT INTO allowances (allowance_name)
VALUES
('Housing'),
('Transport'),
('Medical'),
('Meal'),
('Utility'),
('Bonus');

INSERT INTO deductions (deduction_name)
VALUES
('Tax'),
('Pension'),
('NHF'),
('Insurance'),
('Loan');
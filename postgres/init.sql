CREATE TABLE parks (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    address VARCHAR(255) NOT NULL,
    schedule VARCHAR(255)
);

CREATE TABLE cars (
    id SERIAL PRIMARY KEY,
    number VARCHAR(50) NOT NULL,
    driver_name VARCHAR(255) NOT NULL,
    created_by INTEGER
);

CREATE TABLE park_car (
    park_id INTEGER,
    car_id INTEGER,
    PRIMARY KEY (park_id, car_id),
    FOREIGN KEY (park_id) REFERENCES parks(id) ON DELETE CASCADE,
    FOREIGN KEY (car_id) REFERENCES cars(id) ON DELETE CASCADE
);

CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) CHECK (role IN ('manager', 'driver')) NOT NULL
);

ALTER TABLE cars ADD FOREIGN KEY (created_by) REFERENCES users(id);

-- Добавим тестового менеджера и водителя
INSERT INTO users (username, password, role) VALUES
    ('manager', '$2y$10$u6HRhtZCZmgZdOkDhEN1Re5zKH6F7jCLAkHg1jCN/vjU7Z5DjRkNK', 'manager'),
    ('driver', '$2y$10$gtNUGajcMhM.YtKspF1UeO4b5KYMc/XCA46wulSNo1EIPDSC9eISe', 'driver');
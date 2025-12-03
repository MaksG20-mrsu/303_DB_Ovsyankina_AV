-- Таблица мастеров (кадровый учет)
CREATE TABLE masters (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    full_name TEXT NOT NULL,
    phone TEXT UNIQUE,
    salary_percent REAL NOT NULL CHECK(salary_percent > 0 AND salary_percent <= 100),
    is_active BOOLEAN NOT NULL DEFAULT 1,
    hire_date TEXT NOT NULL DEFAULT (date('now')),
    fire_date TEXT,
    created_at TEXT DEFAULT (datetime('now')),
    CHECK (fire_date IS NULL OR fire_date > hire_date)
);

-- Таблица клиентов
CREATE TABLE clients (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    full_name TEXT NOT NULL,
    phone TEXT UNIQUE NOT NULL,
    email TEXT,
    car_model TEXT NOT NULL,
    car_year INTEGER CHECK(car_year >= 1900),
    license_plate TEXT,
    created_at TEXT DEFAULT (datetime('now'))
);

-- Таблица услуг (справочник услуг)
CREATE TABLE services (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT UNIQUE NOT NULL,
    description TEXT,
    duration_minutes INTEGER NOT NULL CHECK(duration_minutes > 0),
    price REAL NOT NULL CHECK(price >= 0),
    is_active BOOLEAN NOT NULL DEFAULT 1,
    created_at TEXT DEFAULT (datetime('now'))
);

-- Таблица записей (предварительная запись к мастеру)
CREATE TABLE appointments (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    master_id INTEGER NOT NULL,
    client_id INTEGER NOT NULL,
    appointment_date TEXT NOT NULL,
    status TEXT NOT NULL DEFAULT 'scheduled' 
        CHECK(status IN ('scheduled', 'in_progress', 'completed', 'cancelled')),
    total_price REAL NOT NULL DEFAULT 0 CHECK(total_price >= 0),
    notes TEXT,
    created_at TEXT DEFAULT (datetime('now')),
    FOREIGN KEY (master_id) REFERENCES masters(id) ON DELETE RESTRICT,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
    UNIQUE (master_id, appointment_date) -- Учет занятости мастера
);

-- Таблица услуг в записи (выбор нескольких услуг)
CREATE TABLE appointment_services (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    appointment_id INTEGER NOT NULL,
    service_id INTEGER NOT NULL,
    quantity INTEGER NOT NULL DEFAULT 1 CHECK(quantity > 0),
    price_at_time REAL NOT NULL CHECK(price_at_time >= 0),
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE RESTRICT,
    UNIQUE (appointment_id, service_id)
);

-- Таблица выполненных работ (учет фактически выполненных работ)
CREATE TABLE completed_works (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    master_id INTEGER NOT NULL,
    appointment_id INTEGER,
    service_id INTEGER NOT NULL,
    client_id INTEGER NOT NULL,
    work_date TEXT NOT NULL DEFAULT (datetime('now')),
    quantity INTEGER NOT NULL DEFAULT 1 CHECK(quantity > 0),
    actual_price REAL NOT NULL CHECK(actual_price >= 0),
    notes TEXT,
    created_at TEXT DEFAULT (datetime('now')),
    FOREIGN KEY (master_id) REFERENCES masters(id) ON DELETE RESTRICT,
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE SET NULL,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE RESTRICT,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE RESTRICT
);

-- Таблица расчетов зарплаты (расчет зарплаты за период)
CREATE TABLE salary_calculations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    master_id INTEGER NOT NULL,
    period_start TEXT NOT NULL,
    period_end TEXT NOT NULL,
    total_revenue REAL NOT NULL CHECK(total_revenue >= 0),
    salary_percent REAL NOT NULL CHECK(salary_percent > 0 AND salary_percent <= 100),
    calculated_salary REAL NOT NULL CHECK(calculated_salary >= 0),
    calculation_date TEXT NOT NULL DEFAULT (datetime('now')),
    FOREIGN KEY (master_id) REFERENCES masters(id) ON DELETE RESTRICT,
    CHECK (period_end > period_start)
);

-- Индексы для оптимизации запросов
CREATE INDEX idx_masters_active ON masters(is_active);
CREATE INDEX idx_masters_hire_date ON masters(hire_date);
CREATE INDEX idx_appointments_master_date ON appointments(master_id, appointment_date);
CREATE INDEX idx_appointments_status ON appointments(status);
CREATE INDEX idx_completed_works_master_date ON completed_works(master_id, work_date);
CREATE INDEX idx_salary_master_period ON salary_calculations(master_id, period_start, period_end);
CREATE INDEX idx_services_active ON services(is_active);
CREATE INDEX idx_clients_phone ON clients(phone);

-- Заполнение тестовыми данными

-- Мастера (работающие и уволенный для демонстрации)
INSERT INTO masters (full_name, phone, salary_percent, hire_date) VALUES 
('Иванов Петр Сергеевич', '+79161234567', 30.0, '2023-01-15'),
('Смирнова Анна Владимировна', '+79161234568', 35.0, '2023-02-20'),
('Козлов Дмитрий Игоревич', '+79161234569', 28.0, '2023-03-10'),
('Петрова Мария Александровна', '+79161234570', 32.0, '2022-11-05');

-- Увольняем одного мастера (данные сохраняются)
UPDATE masters SET fire_date = '2024-10-15', is_active = 0 WHERE id = 4;

-- Услуги
INSERT INTO services (name, description, duration_minutes, price) VALUES
('Замена моторного масла', 'Полная замена масла и масляного фильтра', 30, 1500.00),
('Замена тормозных колодок', 'Замена передних тормозных колодок', 60, 3000.00),
('Развал-схождение', 'Регулировка углов установки колес', 45, 2500.00),
('Диагностика двигателя', 'Компьютерная диагностика', 90, 4000.00);

-- Клиенты
INSERT INTO clients (full_name, phone, email, car_model, car_year, license_plate) VALUES
('Сидоров Алексей Викторович', '+79031112233', 'sidorov@mail.ru', 'Toyota Camry', 2020, 'А123БВ777'),
('Кузнецова Ольга Сергеевна', '+79031112244', 'kuznetsova@gmail.com', 'Hyundai Solaris', 2021, 'В456ГД777');

-- Записи на обслуживание
INSERT INTO appointments (master_id, client_id, appointment_date, status, total_price) VALUES
(1, 1, '2024-11-25 10:00:00', 'scheduled', 2700.00),
(2, 2, '2024-11-25 14:00:00', 'completed', 4500.00);

-- Услуги в записях (M:N связь)
INSERT INTO appointment_services (appointment_id, service_id, quantity, price_at_time) VALUES
(1, 1, 1, 1500.00),
(1, 3, 1, 2500.00),
(2, 2, 1, 3000.00),
(2, 4, 1, 4000.00);

-- Выполненные работы (включая работы уволенного мастера)
INSERT INTO completed_works (master_id, service_id, client_id, work_date, actual_price) VALUES
(2, 2, 2, '2024-11-25 14:30:00', 3000.00),
(4, 1, 1, '2024-10-10 11:00:00', 1500.00), -- работа уволенного мастера
(4, 3, 2, '2024-10-05 09:00:00', 2500.00); -- работа уволенного мастера

-- Расчеты зарплаты
INSERT INTO salary_calculations (master_id, period_start, period_end, total_revenue, salary_percent, calculated_salary) VALUES
(1, '2024-10-01', '2024-10-31', 50000.00, 30.0, 15000.00),
(2, '2024-10-01', '2024-10-31', 45000.00, 35.0, 15750.00),
(4, '2024-10-01', '2024-10-31', 25000.00, 32.0, 8000.00); -- расчет для уволенного

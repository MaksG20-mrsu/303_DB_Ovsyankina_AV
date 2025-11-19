-- Создание нормализованной базы данных для фильмов

-- Таблица occupations (род занятий)
CREATE TABLE occupations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL UNIQUE
);

-- Таблица users (пользователи)
CREATE TABLE users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    email TEXT UNIQUE NOT NULL,
    gender TEXT CHECK(gender IN ('M', 'F')) NOT NULL,
    register_date TEXT DEFAULT (date('now')),
    occupation_id INTEGER,
    FOREIGN KEY (occupation_id) REFERENCES occupations(id) ON DELETE SET NULL
);

-- Таблица movies (фильмы)
CREATE TABLE movies (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    year INTEGER CHECK(year >= 1888 AND year <= strftime('%Y', 'now')),
    created_date TEXT DEFAULT (datetime('now'))
);

-- Таблица genres (жанры)
CREATE TABLE genres (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL UNIQUE
);

-- Связующая таблица movie_genres (фильмы-жанры)
CREATE TABLE movie_genres (
    movie_id INTEGER NOT NULL,
    genre_id INTEGER NOT NULL,
    PRIMARY KEY (movie_id, genre_id),
    FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE,
    FOREIGN KEY (genre_id) REFERENCES genres(id) ON DELETE CASCADE
);

-- Таблица ratings (рейтинги)
CREATE TABLE ratings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    movie_id INTEGER NOT NULL,
    rating REAL CHECK(rating >= 0 AND rating <= 5) NOT NULL,
    timestamp INTEGER DEFAULT (strftime('%s', 'now')),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE RESTRICT
);

-- Таблица tags (теги)
CREATE TABLE tags (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    movie_id INTEGER NOT NULL,
    tag TEXT NOT NULL,
    timestamp INTEGER DEFAULT (strftime('%s', 'now')),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE
);

-- Индексы для ускорения поиска
CREATE INDEX idx_users_name ON users(name);
CREATE INDEX idx_movies_title ON movies(title);
CREATE INDEX idx_movies_year ON movies(year);
CREATE INDEX idx_ratings_user_id ON ratings(user_id);
CREATE INDEX idx_ratings_movie_id ON ratings(movie_id);
CREATE INDEX idx_tags_user_id ON tags(user_id);
CREATE INDEX idx_tags_movie_id ON tags(movie_id);

-- Заполнение справочников данными
INSERT INTO occupations (name) VALUES 
('administrator'), ('artist'), ('doctor'), ('educator'), ('engineer'),
('entertainment'), ('executive'), ('healthcare'), ('homemaker'), ('lawyer'),
('librarian'), ('marketing'), ('none'), ('other'), ('programmer'),
('retired'), ('salesman'), ('scientist'), ('student'), ('technician'),
('writer');

-- Заполнение жанров
INSERT INTO genres (name) VALUES
('Action'), ('Adventure'), ('Animation'), ('Children'), ('Comedy'),
('Crime'), ('Documentary'), ('Drama'), ('Fantasy'), ('Film-Noir'),
('Horror'), ('Musical'), ('Mystery'), ('Romance'), ('Sci-Fi'),
('Thriller'), ('War'), ('Western');

-- Добавление новых пользователей (я + 4 соседа по группе)
INSERT INTO users (name, email, gender, occupation_id) VALUES
('Александра Овсянкина', 'ovsyankina@example.com', 'F', (SELECT id FROM occupations WHERE name = 'student')),
('Моисеев Ян', 'moiseev@example.com', 'M', (SELECT id FROM occupations WHERE name = 'student')),
('Мулюгин Александр', 'mulyugin@example.com', 'M', (SELECT id FROM occupations WHERE name = 'student')),
('Розанов Ярослав', 'rozanov@example.com', 'M', (SELECT id FROM occupations WHERE name = 'student')),
('Сковородникова Алёна', 'skovorodnikova@example.com', 'F', (SELECT id FROM occupations WHERE name = 'student'));

-- Добавление новых фильмов
INSERT INTO movies (title, year) VALUES
('Космическая одиссея 2024', 2024),
('Тайна старого замка', 2023),
('Ритмы большого города', 2024);

-- Связывание фильмов с жанрами
INSERT INTO movie_genres (movie_id, genre_id) VALUES
((SELECT id FROM movies WHERE title = 'Космическая одиссея 2024'), (SELECT id FROM genres WHERE name = 'Sci-Fi')),
((SELECT id FROM movies WHERE title = 'Космическая одиссея 2024'), (SELECT id FROM genres WHERE name = 'Adventure')),
((SELECT id FROM movies WHERE title = 'Тайна старого замка'), (SELECT id FROM genres WHERE name = 'Mystery')),
((SELECT id FROM movies WHERE title = 'Тайна старого замка'), (SELECT id FROM genres WHERE name = 'Drama')),
((SELECT id FROM movies WHERE title = 'Ритмы большого города'), (SELECT id FROM genres WHERE name = 'Drama')),
((SELECT id FROM movies WHERE title = 'Ритмы большого города'), (SELECT id FROM genres WHERE name = 'Romance'));

-- Добавление отзывов от меня
INSERT INTO ratings (user_id, movie_id, rating) VALUES
((SELECT id FROM users WHERE email = 'ovsyankina@example.com'), (SELECT id FROM movies WHERE title = 'Космическая одиссея 2024'), 4.5),
((SELECT id FROM users WHERE email = 'ovsyankina@example.com'), (SELECT id FROM movies WHERE title = 'Тайна старого замка'), 4.0),
((SELECT id FROM users WHERE email = 'ovsyankina@example.com'), (SELECT id FROM movies WHERE title = 'Ритмы большого города'), 3.5);

-- Добавление тегов
INSERT INTO tags (user_id, movie_id, tag) VALUES
((SELECT id FROM users WHERE email = 'ovsyankina@example.com'), (SELECT id FROM movies WHERE title = 'Космическая одиссея 2024'), 'захватывающий'),
((SELECT id FROM users WHERE email = 'ovsyankina@example.com'), (SELECT id FROM movies WHERE title = 'Тайна старого замка'), 'загадочный'),
((SELECT id FROM users WHERE email = 'ovsyankina@example.com'), (SELECT id FROM movies WHERE title = 'Ритмы большого города'), 'романтичный');

#!/bin/bash

# Используем полный путь к SQLite
SQLITE="C:/sql/sqlite3.exe"

# Создаем базу данных
"$SQLITE" movies_rating.db < db_init.sql

echo "1. Найти все пары пользователей, оценивших один и тот же фильм. Устранить дубликаты, проверить отсутствие пар с самим собой. Для каждой пары должны быть указаны имена пользователей и название фильма, который они ценили. В списке оставить первые 100 записей."
echo "--------------------------------------------------"
"$SQLITE" movies_rating.db -box -echo "SELECT DISTINCT u1.name AS user1, u2.name AS user2, m.title FROM ratings r1 JOIN ratings r2 ON r1.movie_id = r2.movie_id JOIN users u1 ON r1.user_id = u1.id JOIN users u2 ON r2.user_id = u2.id JOIN movies m ON r1.movie_id = m.id WHERE u1.id < u2.id ORDER BY u1.name, u2.name, m.title LIMIT 100;"
echo " "

echo "2. Найти 10 самых старых оценок от разных пользователей, вывести названия фильмов, имена пользователей, оценку, дату отзыва в формате ГГГГ-ММ-ДД."
echo "--------------------------------------------------"
"$SQLITE" movies_rating.db -box -echo "SELECT m.title, u.name, r.rating, strftime('%Y-%m-%d', datetime(r.timestamp, 'unixepoch')) AS rating_date FROM ratings r JOIN movies m ON r.movie_id = m.id JOIN users u ON r.user_id = u.id ORDER BY r.timestamp ASC LIMIT 10;"
echo " "

echo "3. Вывести в одном списке все фильмы с максимальным средним рейтингом и все фильмы с минимальным средним рейтингом. Общий список отсортировать по году выпуска и названию фильма. В зависимости от рейтинга в колонке 'Рекомендуем' для фильмов должно быть написано 'Да' или 'Нет'."
echo "--------------------------------------------------"
"$SQLITE" movies_rating.db -box -echo "WITH avg_ratings AS (SELECT movie_id, AVG(rating) as avg_rating FROM ratings GROUP BY movie_id), max_min AS (SELECT MAX(avg_rating) as max_rating, MIN(avg_rating) as min_rating FROM avg_ratings) SELECT m.title, m.year, ar.avg_rating, CASE WHEN ar.avg_rating = (SELECT max_rating FROM max_min) THEN 'Да' ELSE 'Нет' END AS Рекомендуем FROM movies m JOIN avg_ratings ar ON m.id = ar.movie_id WHERE ar.avg_rating = (SELECT max_rating FROM max_min) OR ar.avg_rating = (SELECT min_rating FROM max_min) ORDER BY m.year, m.title;"
echo " "

echo "4. Вычислить количество оценок и среднюю оценку, которую дали фильмам пользователи-мужчины в период с 2011 по 2014 год."
echo "--------------------------------------------------"
"$SQLITE" movies_rating.db -box -echo "SELECT COUNT(*) as количество_оценок, AVG(r.rating) as средняя_оценка FROM ratings r JOIN users u ON r.user_id = u.id WHERE u.gender = 'M' AND strftime('%Y', datetime(r.timestamp, 'unixepoch')) BETWEEN '2011' AND '2014';"
echo " "

echo "5. Составить список фильмов с указанием средней оценки и количества пользователей, которые их оценили. Полученный список отсортировать по году выпуска и названиям фильмов. В списке оставить первые 20 записей."
echo "--------------------------------------------------"
"$SQLITE" movies_rating.db -box -echo "SELECT m.title, m.year, AVG(r.rating) as средняя_оценка, COUNT(r.user_id) as количество_оценок FROM movies m JOIN ratings r ON m.id = r.movie_id GROUP BY m.id, m.title, m.year ORDER BY m.year, m.title LIMIT 20;"
echo " "

echo "6. Определить самый распространенный жанр фильма и количество фильмов в этом жанре. Отдельную таблицу для жанров не использовать, жанры нужно извлекать из таблицы movies."
echo "--------------------------------------------------"
"$SQLITE" movies_rating.db -box -echo "WITH genre_counts AS (SELECT trim(value) as genre, COUNT(*) as count FROM movies, json_each('[\"' || replace(genres, '|', '\",\"') || '\"]') GROUP BY genre) SELECT genre, count FROM genre_counts ORDER BY count DESC LIMIT 1;"
echo " "

echo "7. Вывести список из 10 последних зарегистрированных пользователей в формате 'Фамилия Имя|Дата регистрации' (сначала фамилия, потом имя)."
echo "--------------------------------------------------"
"$SQLITE" movies_rating.db -box -echo "SELECT SUBSTR(name, INSTR(name, ' ') + 1) || ' ' || SUBSTR(name, 1, INSTR(name, ' ') - 1) || '|' || register_date AS пользователь FROM users ORDER BY register_date DESC LIMIT 10;"
echo " "

echo "8. С помощью рекурсивного CTE определить, на какие дни недели приходился ваш день рождения в каждом году."
echo "--------------------------------------------------"
"$SQLITE" movies_rating.db -box -echo "WITH RECURSIVE years(year) AS (SELECT 2005 UNION ALL SELECT year + 1 FROM years WHERE year < 2025) SELECT year, CASE CAST(strftime('%w', year || '-10-02') AS INTEGER) WHEN 0 THEN 'Воскресенье' WHEN 1 THEN 'Понедельник' WHEN 2 THEN 'Вторник' WHEN 3 THEN 'Среда' WHEN 4 THEN 'Четверг' WHEN 5 THEN 'Пятница' WHEN 6 THEN 'Суббота' END AS день_недели FROM years;"
echo " "

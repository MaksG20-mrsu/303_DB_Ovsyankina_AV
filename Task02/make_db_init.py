import csv
from pathlib import Path

def escape_sql_value(value):
    """Экранирование специальных символов для SQL"""
    if value is None or value == '':
        return 'NULL'
    return str(value).replace("'", "''")

def extract_year_from_title(title):
    """Извлечение года из названия фильма"""
    if '(' in title and ')' in title:
        try:
            year_start = title.rfind('(')
            year_end = title.rfind(')')
            year_str = title[year_start + 1:year_end]
            if year_str.isdigit() and len(year_str) == 4:
                return year_str, title[:year_start].strip()
        except:
            pass
    return 'NULL', title

def generate_db_init():
    """Генерация SQL-скрипта для инициализации БД"""

    # Проверяем наличие каталога dataset
    dataset_path = Path('dataset')
    if not dataset_path.exists():
        print("Ошибка: каталог 'dataset' не найден!")
        return

    # Определяем пути к файлам
    movies_file = dataset_path / 'movies.csv'
    ratings_file = dataset_path / 'ratings.csv'
    tags_file = dataset_path / 'tags.csv'
    users_file = dataset_path / 'users.txt'

    # Проверяем наличие всех необходимых файлов
    required_files = [movies_file, ratings_file, tags_file, users_file]
    for file in required_files:
        if not file.exists():
            print(f"Ошибка: файл {file} не найден!")
            return

    # Создаем SQL-скрипт
    with open('db_init.sql', 'w', encoding='utf-8') as f:
        # Удаляем существующие таблицы
        f.write('-- Удаление существующих таблиц\n')
        f.write('DROP TABLE IF EXISTS tags;\n')
        f.write('DROP TABLE IF EXISTS ratings;\n')
        f.write('DROP TABLE IF EXISTS movies;\n')
        f.write('DROP TABLE IF EXISTS users;\n\n')

        # Создаем таблицы
        f.write('-- Создание таблиц\n')
        f.write('''CREATE TABLE movies (
    id INTEGER PRIMARY KEY,
    title TEXT NOT NULL,
    year INTEGER,
    genres TEXT
);\n\n''')

        f.write('''CREATE TABLE users (
    id INTEGER PRIMARY KEY,
    name TEXT NOT NULL,
    email TEXT,
    gender TEXT,
    register_date TEXT,
    occupation TEXT
);\n\n''')

        f.write('''CREATE TABLE ratings (
    id INTEGER PRIMARY KEY,
    user_id INTEGER NOT NULL,
    movie_id INTEGER NOT NULL,
    rating REAL NOT NULL,
    timestamp INTEGER NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (movie_id) REFERENCES movies(id)
);\n\n''')

        f.write('''CREATE TABLE tags (
    id INTEGER PRIMARY KEY,
    user_id INTEGER NOT NULL,
    movie_id INTEGER NOT NULL,
    tag TEXT NOT NULL,
    timestamp INTEGER NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (movie_id) REFERENCES movies(id)
);\n\n''')

        # Загружаем данные из файлов dataset

        # Загрузка users из TXT файла с разделителем |
        f.write('-- Загрузка данных в таблицу users\n')
        users_count = 0
        try:
            with open(users_file, 'r', encoding='utf-8') as users_txt:
                for line in users_txt:
                    line = line.strip()
                    if not line:
                        continue

                    # Разделяем строку по |
                    parts = line.split('|')
                    if len(parts) >= 6:
                        user_id = parts[0]
                        name = escape_sql_value(parts[1])
                        email = escape_sql_value(parts[2])
                        gender = escape_sql_value(parts[3])
                        register_date = escape_sql_value(parts[4])
                        occupation = escape_sql_value(parts[5])

                        f.write(f"INSERT INTO users (id, name, email, gender, register_date, occupation) VALUES ({user_id}, '{name}', '{email}', '{gender}', '{register_date}', '{occupation}');\n")
                        users_count += 1
            print(f"Загружено {users_count} пользователей")
        except Exception as e:
            print(f"Ошибка при загрузке users: {e}")

        # Загрузка movies из CSV файла (без заголовков)
        f.write('\n-- Загрузка данных в таблицу movies\n')
        movies_count = 0
        try:
            with open(movies_file, 'r', encoding='utf-8') as movies_csv:
                reader = csv.reader(movies_csv)
                for row in reader:
                    if len(row) >= 3:
                        movie_id = row[0]
                        title = escape_sql_value(row[1])
                        genres = escape_sql_value(row[2])

                        # Извлекаем год из названия
                        year, clean_title = extract_year_from_title(title)

                        f.write(f"INSERT INTO movies (id, title, year, genres) VALUES ({movie_id}, '{clean_title}', {year}, '{genres}');\n")
                        movies_count += 1
            print(f"Загружено {movies_count} фильмов")
        except Exception as e:
            print(f"Ошибка при загрузке movies: {e}")

        # Загрузка ratings из CSV файла (без заголовков)
        f.write('\n-- Загрузка данных в таблицу ratings\n')
        ratings_count = 0
        try:
            with open(ratings_file, 'r', encoding='utf-8') as ratings_csv:
                reader = csv.reader(ratings_csv)
                for row_num, row in enumerate(reader, 1):
                    if len(row) >= 4:
                        user_id = row[0]
                        movie_id = row[1]
                        rating = row[2]
                        timestamp = row[3]

                        f.write(f"INSERT INTO ratings (id, user_id, movie_id, rating, timestamp) VALUES ({row_num}, {user_id}, {movie_id}, {rating}, {timestamp});\n")
                        ratings_count += 1
            print(f"Загружено {ratings_count} рейтингов")
        except Exception as e:
            print(f"Ошибка при загрузке ratings: {e}")

        # Загрузка tags из CSV файла (без заголовков)
        f.write('\n-- Загрузка данных в таблицу tags\n')
        tags_count = 0
        try:
            with open(tags_file, 'r', encoding='utf-8') as tags_csv:
                reader = csv.reader(tags_csv)
                for row_num, row in enumerate(reader, 1):
                    if len(row) >= 4:
                        user_id = row[0]
                        movie_id = row[1]
                        tag = escape_sql_value(row[2])
                        timestamp = row[3]

                        f.write(f"INSERT INTO tags (id, user_id, movie_id, tag, timestamp) VALUES ({row_num}, {user_id}, {movie_id}, '{tag}', {timestamp});\n")
                        tags_count += 1
            print(f"Загружено {tags_count} тегов")
        except Exception as e:
            print(f"Ошибка при загрузке tags: {e}")

        f.write('\n-- Коммит изменений\n')
        f.write('COMMIT;\n')

if __name__ == "__main__":
    print("Генерация SQL-скрипта для инициализации БД...")
    generate_db_init()
    print("SQL-скрипт db_init.sql успешно создан!")

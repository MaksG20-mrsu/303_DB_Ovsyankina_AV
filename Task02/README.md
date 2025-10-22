# Лабораторная работа 2. Подготовка скриптов для создания таблиц и добавления данных

## Требования

Для корректной работы скрипта db_init.bat необходимо установленное программное обеспечение:

### Обязательные компоненты:
1. Python v.3 или выше
2. SQLite3

### Дополнительные требования:
- Windows, Linux или macOS
- Доступ на запись в текущую директорию
- Исходные файлы данных в папке Task02

## Структура БД:

*movies*. Поля id (primary key), title, year, genres.
*ratings*. Поля id (primary key), user_id, movie_id, rating, timestamp.
*tags*. Поля id (primary key), user_id, movie_id, tag, timestamp.
*users*. Поля id (primary key), name, email, gender, register_date, occupation.

## Запуск скрипта

1. Выполнить все требования
2. Перейдите в директорию Task02
3. Выполнить команду: ./db_init.bat
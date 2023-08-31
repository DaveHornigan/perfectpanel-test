***Task 1:***
```sql
SELECT users.id as ID, CONCAT(users.first_name, ' ', users.last_name) as Name, books.author as Author, GROUP_CONCAT(books.name) as Books FROM users JOIN user_books ON user_books.user_id = users.id JOIN books ON books.id = user_books.book_id WHERE (YEAR(CURRENT_DATE) - YEAR(users.birthday)) BETWEEN 7 AND 17 GROUP BY books.author, users.id HAVING COUNT(books.id) = 2 AND COUNT(DISTINCT(books.author)) = 1;
```
***Task 2:***
How to run:
```shell

```
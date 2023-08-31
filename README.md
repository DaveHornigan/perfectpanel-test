### Task 1:
```sql
SELECT users.id as ID, CONCAT(users.first_name, ' ', users.last_name) as Name, books.author as Author, GROUP_CONCAT(books.name) as Books FROM users JOIN user_books ON user_books.user_id = users.id JOIN books ON books.id = user_books.book_id WHERE (YEAR(CURRENT_DATE) - YEAR(users.birthday)) BETWEEN 7 AND 17 GROUP BY books.author, users.id HAVING COUNT(books.id) = 2 AND COUNT(DISTINCT(books.author)) = 1;
```

### Task 2:
#### How to prepare to work:
```shell
composer install
docker compose up -d --build
```
you can change `TEST_USER_TOKEN` or `SERVICE_COMMISSION_PERCENT` in `.env` file if you need it

#### Usage:
```shell
curl -H 'Authorization: Bearer {TOKEN STORED IN TEST_USER_TOKEN}' \
    'http://localhost/api/v1?method=rates' # Get all rates
```
```shell
curl -H 'Authorization: Bearer {TOKEN STORED IN TEST_USER_TOKEN}' \
    'http://localhost/api/v1?method=rates&currency=RUB,USD,ARS' # Get only selected currencies
```
```shell
curl -X POST -H 'Authorization: Bearer {TOKEN STORED IN TEST_USER_TOKEN}' -H "Content-Type: application/json" \
    -d '{"currency_from": "USD", "currency_to": "BTC", "value": 1.00}' 'http://localhost/api/v1?method=convert' # Convert currency
```

P.S. It is possible that the commission calculation is mixed up
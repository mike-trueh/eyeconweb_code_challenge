## Тестовое задание Eyeconweb

### Запуск сервиса

Для работы в докере используется обертка laravel sail

```shell
chmod 755 run.sh && ./run.sh
```

При наличии активного веб сервера на компьютере в файл .env добавить строку:

```dotenv
APP_PORT=81
```

Документация по API находится по адресу: [http://localhost/api/documentation/](http://localhost/api/documentation/)

### Запуск тестов

```shell
./venodor/bin/sail artisan test
```

### Запуск воркера и работа задач в фоне

В файле .env поправить

```dotenv
QUEUE_CONNECTION=redis
```

Далее запустить воркер

```shell
./venodor/bin/sail artisan queue:work
```

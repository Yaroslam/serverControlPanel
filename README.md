# Yaroslam/SSH2
 
## Установка  
для работы библиотеки необходимо: 
1. установленное расширение [SSH2](https://www.php.net/manual/ru/book.ssh2.php)  
1. минимальная версия PHP - 8.1  
1. `composer require yaroslam/ssh2`

## Документация
### Подключение  
Что бы инициализировать подключение к удаленному серверу необходимо создать экземпляр ConnectionInterface и обратиться 
к методу connect, передав в него $connectProperties и $connection, являющийся результатом выполнения функции ssh2_connect() 
модуля SSH2.  

Список экземпляров и формата $connectProperties:
1. UserPasswordConnection ["user" => ' ', 'password' => ' ']

### Сессии
Для создания сессии необходимо создать экземпляр AbstractSession, передав в конструктор экземпляр ConnectionInterface и $connectProperties
формата ['port' => port, 'host' => 'ip',
'properties' => формат $connectProperties для выбранного подтипа ConnectionInterface  
Типы сессий 
1. Session - не сохраняет контекст выполнения и позволяет использовать только exec() метод
2. ChainSession - сохраняет контекст выполнения и позволяет конструировать сложные сценарии с использованием if, for, switch case конструкций  

При использовании любой сессии для получения результата необходимо в конце вызвать метод apply()  
Пример: `$session->exec("ls -la")->apply()`

### Команды

### Функции


## Тестирование
Для тестирования используется PHPUnit


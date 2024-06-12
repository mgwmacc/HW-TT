The description as it is:
"
Create two applications using Symfony5 (or 6/7) framework:
1. server with API entries
2. client
1. Server stores users and groups in MySQL database.
user table: id, name, email
group table: id, name
2. Client accessing the server through the API using CLI:
2.1. should be able to add, edit, delete users and groups on the server (well,
CRUD)
2.2 should be able to get report with the list of users of each group.
The ready-made project should be able to start using Docker.
"


Please do next to run the project:

(Server and Client functionality included in one (current) project)

- composer install
- Run "docker compose up"
- Run any needed services from the /docker-compose.yml
- PhpMyadmin should be available from http://localhost:81 (if started in services)
- Run migrations
- Just in case: the project DB Schema (SQL) is in the Project root.
- Nelmio(Swagger) Project documentation is available here: http://localhost/api/doc
- Tests for API Endpoints added
- Tests for Commands added

# StudentTasker

StudentTasker to aplikacja internetowa do zarządzania projektami i zadaniami studenckimi. Projekt został przygotowany jako aplikacja zaliczeniowa z przedmiotu Wstęp do projektowania aplikacji internetowych.

Aplikacja umożliwia logowanie użytkowników, obsługę sesji, kontrolę ról, zarządzanie użytkownikami, przeglądanie projektów, dodawanie, edytowanie i usuwanie zadań oraz zmianę statusu zadania z wykorzystaniem JavaScript Fetch API.

## Technologie

W projekcie wykorzystano:

* Docker
* Git
* HTML5
* CSS3
* JavaScript
* Fetch API
* PHP obiektowy
* PostgreSQL
* Apache

Projekt nie wykorzystuje frameworków ani gotowych szablonów.

## Architektura aplikacji

Aplikacja została przygotowana w architekturze MVC.

Główne warstwy aplikacji:

* `public/` — punkt wejścia aplikacji, pliki publiczne CSS i JS
* `app/Controllers/` — kontrolery obsługujące żądania użytkownika
* `app/Models/` — miejsce na modele domenowe
* `app/Repositories/` — klasy odpowiedzialne za komunikację z bazą danych
* `app/Services/` — logika usługowa, np. autoryzacja
* `app/Views/` — widoki aplikacji
* `app/Core/` — podstawowe klasy aplikacji, np. router, kontroler bazowy, połączenie z bazą
* `config/` — konfiguracja aplikacji
* `database/` — plik SQL tworzący bazę danych
* `docker/` — konfiguracja Dockera i Apache

## Funkcjonalności

Aplikacja zawiera:

* logowanie użytkowników
* obsługę sesji
* wylogowanie
* role użytkowników:

  * administrator
  * lider
  * student
* kontrolę dostępu do stron
* stronę błędu 401 dla użytkownika niezalogowanego
* stronę błędu 403 dla braku uprawnień
* stronę błędu 404 dla nieistniejącej strony
* panel administratora
* zarządzanie użytkownikami przez administratora
* aktywowanie i dezaktywowanie kont użytkowników
* listę projektów
* szczegóły projektu
* listę zadań w projekcie
* dodawanie zadań
* edycję zadań
* usuwanie zadań
* zmianę statusu zadania przez Fetch API bez przeładowania strony

## Role użytkowników

### Administrator

Administrator może:

* logować się do systemu
* przeglądać dashboard
* przeglądać projekty
* zarządzać użytkownikami
* aktywować i dezaktywować konta
* dodawać, edytować i usuwać zadania
* zmieniać statusy zadań

### Lider

Lider może:

* logować się do systemu
* przeglądać dashboard
* przeglądać przypisane projekty
* zarządzać zadaniami w dostępnych projektach

### Student

Student może:

* logować się do systemu
* przeglądać dashboard
* przeglądać przypisane projekty
* zmieniać statusy zadań w dostępnych projektach

## Dane testowe

Hasło dla kont testowych:

```text
password
```

Konta testowe:

```text
Administrator:
admin@studenttasker.local

Lider:
anna@studenttasker.local

Student:
jan@studenttasker.local
```

Konto testowe nieaktywne:

```text
ewa@studenttasker.local
```

## Uruchomienie projektu

Do uruchomienia projektu wymagane są:

* Docker Desktop
* Git
* przeglądarka internetowa

Aby uruchomić projekt, należy sklonować repozytorium lub pobrać pliki projektu, a następnie w katalogu głównym projektu wykonać polecenie:

```bash
docker compose up
```

Aplikacja będzie dostępna pod adresem:

```text
http://localhost:8080
```

Baza danych PostgreSQL będzie dostępna na porcie:

```text
5432
```

## Konfiguracja środowiska

Przykładowe zmienne środowiskowe znajdują się w pliku:

```text
.env.example
```

Przykładowa konfiguracja:

```text
APP_NAME=StudentTasker
APP_ENV=development
APP_DEBUG=true

DB_HOST=db
DB_PORT=5432
DB_NAME=studenttasker
DB_USER=studenttasker_user
DB_PASSWORD=studenttasker_password
```

## Baza danych

Baza danych znajduje się w pliku:

```text
database/init.sql
```

Plik SQL tworzy:

* tabele
* relacje
* dane testowe
* widoki
* funkcje
* wyzwalacze

W bazie występują relacje:

* jeden do jednego
* jeden do wielu
* wiele do wielu

Przykłady relacji:

* `users` — `user_profiles`: relacja jeden do jednego
* `users` — `projects`: relacja jeden do wielu
* `projects` — `users` przez `project_members`: relacja wiele do wielu
* `projects` — `tasks`: relacja jeden do wielu
* `tasks` — `comments`: relacja jeden do wielu

Baza zawiera widoki:

* `view_project_tasks`
* `view_user_task_summary`

Baza zawiera funkcję:

* `get_project_progress(project_identifier INTEGER)`

Baza zawiera wyzwalacze:

* `trg_users_updated_at`
* `trg_projects_updated_at`
* `trg_tasks_updated_at`
* `trg_task_status_change`

## Fetch API

W projekcie wykorzystano JavaScript Fetch API do zmiany statusu zadania bez przeładowania strony.

Plik JavaScript:

```text
public/js/tasks.js
```

Endpoint backendowy:

```text
POST /api/tasks/status
```

## Scenariusz testowy

### 1. Logowanie

1. Wejść na stronę:

   ```text
   http://localhost:8080/login
   ```
2. Wpisać dane administratora:

   ```text
   admin@studenttasker.local
   password
   ```
3. Kliknąć przycisk logowania.
4. Użytkownik powinien zostać przekierowany do dashboardu.

### 2. Sesja użytkownika

1. Po zalogowaniu wejść na:

   ```text
   http://localhost:8080/dashboard
   ```
2. Dashboard powinien pokazać dane zalogowanego użytkownika.
3. Po kliknięciu „Wyloguj się” użytkownik powinien zostać wylogowany.

### 3. Błąd 401

1. Wylogować się z aplikacji.
2. Wejść na:

   ```text
   http://localhost:8080/dashboard
   ```
3. Powinna pojawić się strona błędu 401.

### 4. Błąd 403

1. Zalogować się jako student:

   ```text
   jan@studenttasker.local
   password
   ```
2. Wejść na:

   ```text
   http://localhost:8080/admin/users
   ```
3. Powinna pojawić się strona błędu 403.

### 5. Błąd 404

1. Wejść na nieistniejący adres:

   ```text
   http://localhost:8080/nie-ma-takiej-strony
   ```
2. Powinna pojawić się strona błędu 404.

### 6. Zarządzanie użytkownikami

1. Zalogować się jako administrator.
2. Wejść na:

   ```text
   http://localhost:8080/admin/users
   ```
3. Dezaktywować konto studenta.
4. Wylogować się.
5. Spróbować zalogować się jako student.
6. Logowanie powinno zostać zablokowane.
7. Ponownie zalogować się jako administrator.
8. Aktywować konto studenta.
9. Student powinien móc ponownie się zalogować.

### 7. CRUD zadań

1. Zalogować się jako administrator.
2. Wejść na listę projektów:

   ```text
   http://localhost:8080/projects
   ```
3. Wejść w szczegóły projektu.
4. Dodać nowe zadanie.
5. Edytować zadanie.
6. Zmienić status zadania.
7. Usunąć zadanie.

### 8. Fetch API

1. Wejść w szczegóły projektu.
2. Zmienić status zadania z listy rozwijanej.
3. Strona nie powinna się przeładować.
4. Po odświeżeniu strony status powinien pozostać zmieniony.

## Diagramy

W repozytorium należy umieścić:

* diagram ERD bazy danych
* diagram architektury aplikacji

Proponowana lokalizacja:

```text
docs/erd.png
docs/architecture.png
```

Źródła diagramów można przygotować w formacie:

```text
docs/erd.drawio
docs/architecture.drawio
```

## Screeny aplikacji

W repozytorium należy umieścić screeny aplikacji w wersji webowej i mobilnej.

Proponowana lokalizacja:

```text
docs/screens/
```

## Checklista wymagań

* [x] Docker
* [x] Git
* [x] HTML5
* [x] CSS
* [x] JavaScript
* [x] Fetch API
* [x] PHP obiektowy
* [x] PostgreSQL
* [x] Architektura MVC
* [x] Logowanie
* [x] Sesje
* [x] Wylogowanie
* [x] Role użytkowników
* [x] Uprawnienia użytkowników
* [x] Zarządzanie użytkownikami
* [x] CRUD zadań
* [x] Relacje w bazie danych
* [x] Relacja jeden do jednego
* [x] Relacja jeden do wielu
* [x] Relacja wiele do wielu
* [x] Minimum 2 widoki SQL
* [x] Minimum 1 funkcja SQL
* [x] Minimum 1 wyzwalacz SQL
* [x] Transakcje w pliku SQL
* [x] Klucze główne i obce
* [x] JOIN w zapytaniach SQL
* [x] Responsywność
* [x] Media queries
* [x] Strona 401
* [x] Strona 403
* [x] Strona 404
* [ ] Diagram ERD
* [ ] Diagram architektury
* [ ] Screeny aplikacji webowej i mobilnej
* [ ] Finalny eksport bazy danych SQL
* [ ] Końcowe sprawdzenie projektu

## Autor

Szymon Róg

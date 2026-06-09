# Diagram architektury aplikacji StudentTasker

Aplikacja StudentTasker została przygotowana w architekturze MVC.

## Warstwy aplikacji

```text
Użytkownik / Przeglądarka
        |
        v
public/index.php
        |
        v
Router
        |
        v
Controller
        |
        v
Service / Repository
        |
        v
PostgreSQL Database
        |
        v
View + HTML/CSS/JavaScript
```

## Opis warstw

### public

Folder public jest publicznym punktem wejścia aplikacji. Znajduje się tam plik index.php, który uruchamia router aplikacji. W tym folderze znajdują się także pliki CSS i JavaScript.

### Router

Router odpowiada za dopasowanie adresu URL do odpowiedniego kontrolera i metody.

### Controllers

Kontrolery obsługują żądania użytkownika, sprawdzają uprawnienia, pobierają dane z repozytoriów i przekazują dane do widoków.

### Services

Serwisy zawierają logikę aplikacji niezwiązaną bezpośrednio z widokiem, np. logowanie i obsługę sesji.

### Repositories

Repozytoria odpowiadają za komunikację z bazą danych PostgreSQL. Wykorzystują zapytania SQL i klasę Database.

### Views

Widoki odpowiadają za prezentację danych użytkownikowi. Są przygotowane w HTML i PHP.

### Database

Baza danych PostgreSQL przechowuje dane użytkowników, ról, projektów, zadań, komentarzy i logów aktywności.
# Opis diagramu ERD — StudentTasker

Baza danych aplikacji StudentTasker została zaprojektowana jako relacyjna baza danych PostgreSQL. Struktura bazy spełnia wymagania normalizacji oraz zawiera relacje jeden do jednego, jeden do wielu i wiele do wielu.

## Główne tabele

### roles

Tabela przechowuje role użytkowników systemu.

Pola:

- id
- name
- description

Relacje:

- jedna rola może być przypisana do wielu użytkowników

Typ relacji:

- jeden do wielu: roles → users

---

### users

Tabela przechowuje konta użytkowników.

Pola:

- id
- role_id
- email
- password_hash
- first_name
- last_name
- is_active
- created_at
- updated_at

Relacje:

- użytkownik należy do jednej roli
- użytkownik posiada jeden profil
- użytkownik może być właścicielem wielu projektów
- użytkownik może należeć do wielu projektów
- użytkownik może mieć przypisane wiele zadań
- użytkownik może dodawać wiele komentarzy
- użytkownik może mieć wiele wpisów aktywności

---

### user_profiles

Tabela przechowuje dodatkowe informacje o użytkowniku.

Pola:

- id
- user_id
- phone
- bio
- avatar_url

Relacje:

- jeden profil należy do jednego użytkownika

Typ relacji:

- jeden do jednego: users → user_profiles

---

### projects

Tabela przechowuje projekty.

Pola:

- id
- owner_id
- name
- description
- start_date
- end_date
- created_at
- updated_at

Relacje:

- projekt ma jednego właściciela
- projekt może mieć wielu członków
- projekt może mieć wiele zadań

Typ relacji:

- jeden do wielu: users → projects
- jeden do wielu: projects → tasks

---

### project_members

Tabela pośrednia dla relacji wiele do wielu między użytkownikami i projektami.

Pola:

- project_id
- user_id
- member_role
- joined_at

Relacje:

- jeden projekt może mieć wielu użytkowników
- jeden użytkownik może należeć do wielu projektów

Typ relacji:

- wiele do wielu: users ↔ projects

---

### task_statuses

Tabela przechowuje statusy zadań.

Pola:

- id
- name
- sort_order

Relacje:

- jeden status może być przypisany do wielu zadań

Typ relacji:

- jeden do wielu: task_statuses → tasks

---

### tasks

Tabela przechowuje zadania w projektach.

Pola:

- id
- project_id
- status_id
- assigned_user_id
- title
- description
- priority
- due_date
- created_at
- updated_at

Relacje:

- zadanie należy do jednego projektu
- zadanie ma jeden status
- zadanie może być przypisane do jednego użytkownika
- zadanie może mieć wiele komentarzy
- zadanie może mieć wiele wpisów aktywności

Typ relacji:

- jeden do wielu: projects → tasks
- jeden do wielu: task_statuses → tasks
- jeden do wielu: users → tasks
- jeden do wielu: tasks → comments
- jeden do wielu: tasks → activity_logs

---

### comments

Tabela przechowuje komentarze do zadań.

Pola:

- id
- task_id
- user_id
- content
- created_at

Relacje:

- komentarz należy do jednego zadania
- komentarz należy do jednego użytkownika

Typ relacji:

- jeden do wielu: tasks → comments
- jeden do wielu: users → comments

---

### activity_logs

Tabela przechowuje logi aktywności.

Pola:

- id
- user_id
- task_id
- action
- description
- created_at

Relacje:

- wpis aktywności może dotyczyć jednego użytkownika
- wpis aktywności może dotyczyć jednego zadania

Typ relacji:

- jeden do wielu: users → activity_logs
- jeden do wielu: tasks → activity_logs

---

## Widoki SQL

Baza zawiera minimum dwa widoki SQL:

- view_project_tasks
- view_user_task_summary

Widoki wykorzystują złączenia JOIN między wieloma tabelami.

---

## Funkcje SQL

Baza zawiera funkcję:

- get_project_progress(project_identifier INTEGER)

Funkcja oblicza procent ukończenia projektu na podstawie liczby zadań oznaczonych jako wykonane.

---

## Wyzwalacze SQL

Baza zawiera wyzwalacze:

- trg_users_updated_at
- trg_projects_updated_at
- trg_tasks_updated_at
- trg_task_status_change

Wyzwalacze aktualizują datę modyfikacji rekordów oraz zapisują zmianę statusu zadania w tabeli activity_logs.

---

## Relacje wymagane w projekcie

Relacja jeden do jednego:

- users → user_profiles

Relacja jeden do wielu:

- roles → users
- users → projects
- projects → tasks
- task_statuses → tasks
- tasks → comments

Relacja wiele do wielu:

- users ↔ projects przez tabelę project_members
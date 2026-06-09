DROP VIEW IF EXISTS view_user_task_summary;
DROP VIEW IF EXISTS view_project_tasks;

DROP TABLE IF EXISTS activity_logs;
DROP TABLE IF EXISTS comments;
DROP TABLE IF EXISTS tasks;
DROP TABLE IF EXISTS task_statuses;
DROP TABLE IF EXISTS project_members;
DROP TABLE IF EXISTS projects;
DROP TABLE IF EXISTS user_profiles;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS roles;

CREATE TABLE roles (
    id SERIAL PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description VARCHAR(255)
);

CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    role_id INTEGER NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(80) NOT NULL,
    last_name VARCHAR(80) NOT NULL,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_users_roles
        FOREIGN KEY (role_id)
        REFERENCES roles(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
);

CREATE TABLE user_profiles (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL UNIQUE,
    phone VARCHAR(30),
    bio TEXT,
    avatar_url VARCHAR(255),

    CONSTRAINT fk_profiles_users
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
);

CREATE TABLE projects (
    id SERIAL PRIMARY KEY,
    owner_id INTEGER NOT NULL,
    name VARCHAR(120) NOT NULL,
    description TEXT,
    start_date DATE,
    end_date DATE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_projects_owner
        FOREIGN KEY (owner_id)
        REFERENCES users(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
);

CREATE TABLE project_members (
    project_id INTEGER NOT NULL,
    user_id INTEGER NOT NULL,
    member_role VARCHAR(50) NOT NULL DEFAULT 'member',
    joined_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (project_id, user_id),

    CONSTRAINT fk_project_members_projects
        FOREIGN KEY (project_id)
        REFERENCES projects(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT fk_project_members_users
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
);

CREATE TABLE task_statuses (
    id SERIAL PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    sort_order INTEGER NOT NULL
);

CREATE TABLE tasks (
    id SERIAL PRIMARY KEY,
    project_id INTEGER NOT NULL,
    status_id INTEGER NOT NULL,
    assigned_user_id INTEGER,
    title VARCHAR(160) NOT NULL,
    description TEXT,
    priority VARCHAR(30) NOT NULL DEFAULT 'normal',
    due_date DATE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_tasks_projects
        FOREIGN KEY (project_id)
        REFERENCES projects(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT fk_tasks_statuses
        FOREIGN KEY (status_id)
        REFERENCES task_statuses(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_tasks_assigned_user
        FOREIGN KEY (assigned_user_id)
        REFERENCES users(id)
        ON UPDATE CASCADE
        ON DELETE SET NULL
);

CREATE TABLE comments (
    id SERIAL PRIMARY KEY,
    task_id INTEGER NOT NULL,
    user_id INTEGER NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_comments_tasks
        FOREIGN KEY (task_id)
        REFERENCES tasks(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT fk_comments_users
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
);

CREATE TABLE activity_logs (
    id SERIAL PRIMARY KEY,
    user_id INTEGER,
    task_id INTEGER,
    action VARCHAR(120) NOT NULL,
    description TEXT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_activity_users
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON UPDATE CASCADE
        ON DELETE SET NULL,

    CONSTRAINT fk_activity_tasks
        FOREIGN KEY (task_id)
        REFERENCES tasks(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
);

CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trg_users_updated_at
BEFORE UPDATE ON users
FOR EACH ROW
EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER trg_projects_updated_at
BEFORE UPDATE ON projects
FOR EACH ROW
EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER trg_tasks_updated_at
BEFORE UPDATE ON tasks
FOR EACH ROW
EXECUTE FUNCTION update_updated_at_column();

CREATE OR REPLACE FUNCTION log_task_status_change()
RETURNS TRIGGER AS $$
DECLARE
    old_status_name VARCHAR(50);
    new_status_name VARCHAR(50);
BEGIN
    IF OLD.status_id IS DISTINCT FROM NEW.status_id THEN
        SELECT name INTO old_status_name FROM task_statuses WHERE id = OLD.status_id;
        SELECT name INTO new_status_name FROM task_statuses WHERE id = NEW.status_id;

        INSERT INTO activity_logs (user_id, task_id, action, description)
        VALUES (
            NEW.assigned_user_id,
            NEW.id,
            'TASK_STATUS_CHANGED',
            'Status zadania "' || NEW.title || '" zmieniono z "' || old_status_name || '" na "' || new_status_name || '".'
        );
    END IF;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trg_task_status_change
AFTER UPDATE OF status_id ON tasks
FOR EACH ROW
EXECUTE FUNCTION log_task_status_change();

CREATE OR REPLACE FUNCTION get_project_progress(project_identifier INTEGER)
RETURNS NUMERIC AS $$
DECLARE
    all_tasks_count INTEGER;
    done_tasks_count INTEGER;
    progress NUMERIC;
BEGIN
    SELECT COUNT(*)
    INTO all_tasks_count
    FROM tasks
    WHERE project_id = project_identifier;

    IF all_tasks_count = 0 THEN
        RETURN 0;
    END IF;

    SELECT COUNT(*)
    INTO done_tasks_count
    FROM tasks t
    JOIN task_statuses ts ON t.status_id = ts.id
    WHERE t.project_id = project_identifier
      AND ts.name = 'Zrobione';

    progress := ROUND((done_tasks_count::NUMERIC / all_tasks_count::NUMERIC) * 100, 2);

    RETURN progress;
END;
$$ LANGUAGE plpgsql;

CREATE VIEW view_project_tasks AS
SELECT
    p.id AS project_id,
    p.name AS project_name,
    t.id AS task_id,
    t.title AS task_title,
    t.priority,
    t.due_date,
    ts.name AS status_name,
    u.first_name || ' ' || u.last_name AS assigned_user,
    p.created_at AS project_created_at,
    t.updated_at AS task_updated_at
FROM projects p
JOIN tasks t ON p.id = t.project_id
JOIN task_statuses ts ON t.status_id = ts.id
LEFT JOIN users u ON t.assigned_user_id = u.id;

CREATE VIEW view_user_task_summary AS
SELECT
    u.id AS user_id,
    u.first_name || ' ' || u.last_name AS full_name,
    u.email,
    r.name AS role_name,
    COUNT(t.id) AS all_tasks,
    COUNT(CASE WHEN ts.name = 'Do zrobienia' THEN 1 END) AS todo_tasks,
    COUNT(CASE WHEN ts.name = 'W trakcie' THEN 1 END) AS in_progress_tasks,
    COUNT(CASE WHEN ts.name = 'Zrobione' THEN 1 END) AS done_tasks
FROM users u
JOIN roles r ON u.role_id = r.id
LEFT JOIN tasks t ON u.id = t.assigned_user_id
LEFT JOIN task_statuses ts ON t.status_id = ts.id
GROUP BY u.id, u.first_name, u.last_name, u.email, r.name;

BEGIN TRANSACTION ISOLATION LEVEL READ COMMITTED;

INSERT INTO roles (name, description) VALUES
('admin', 'Administrator systemu zarządzający użytkownikami i projektami.'),
('leader', 'Lider projektu zarządzający członkami i zadaniami.'),
('student', 'Zwykły użytkownik wykonujący przypisane zadania.');

INSERT INTO task_statuses (name, sort_order) VALUES
('Do zrobienia', 1),
('W trakcie', 2),
('Zrobione', 3);

INSERT INTO users (role_id, email, password_hash, first_name, last_name, is_active) VALUES
(1, 'admin@studenttasker.local', '$2y$10$jGn5kbiGKAaPBeKmpCBBeeERdmqwjR1ftQZqOPNbumJiD.bpwO4e', 'Szymon', 'Rog', TRUE),
(2, 'anna@studenttasker.local', '$2y$10$jGn5kbiGKAaPBeKmpCBBeeERdmqwjR1ftQZqOPNbumJiD.bpwO4e', 'Anna', 'Kowalska', TRUE),
(3, 'jan@studenttasker.local', '$2y$10$jGn5kbiGKAaPBeKmpCBBeeERdmqwjR1ftQZqOPNbumJiD.bpwO4e', 'Jan', 'Nowak', TRUE),
(3, 'ewa@studenttasker.local', '$2y$10$jGn5kbiGKAaPBeKmpCBBeeERdmqwjR1ftQZqOPNbumJiD.bpwO4e', 'Ewa', 'Zielinska', FALSE);

INSERT INTO user_profiles (user_id, phone, bio, avatar_url) VALUES
(1, '500100100', 'Administrator projektu StudentTasker.', NULL),
(2, '500200200', 'Liderka projektu odpowiedzialna za organizację zadań.', NULL),
(3, '500300300', 'Student realizujący zadania projektowe.', NULL),
(4, '500400400', 'Konto testowe zablokowane.', NULL);

INSERT INTO projects (owner_id, name, description, start_date, end_date) VALUES
(1, 'Projekt PHP', 'Aplikacja zaliczeniowa tworzona w PHP obiektowym i architekturze MVC.', '2026-01-10', '2026-02-15'),
(2, 'Baza PostgreSQL', 'Projekt relacyjnej bazy danych z widokami, funkcją i wyzwalaczem.', '2026-01-12', '2026-02-10'),
(1, 'Dokumentacja projektu', 'README, diagram ERD, scenariusze testowe i screeny aplikacji.', '2026-01-15', '2026-02-20');

INSERT INTO project_members (project_id, user_id, member_role) VALUES
(1, 1, 'owner'),
(1, 2, 'leader'),
(1, 3, 'member'),
(2, 2, 'owner'),
(2, 3, 'member'),
(3, 1, 'owner'),
(3, 2, 'member');

INSERT INTO tasks (project_id, status_id, assigned_user_id, title, description, priority, due_date) VALUES
(1, 1, 1, 'Utworzyć diagram ERD', 'Przygotowanie diagramu relacji bazy danych.', 'high', '2026-01-20'),
(1, 2, 2, 'Logowanie użytkownika', 'Implementacja logowania, sesji i wylogowania.', 'high', '2026-01-25'),
(1, 3, 1, 'Konfiguracja Dockera', 'Przygotowanie docker-compose oraz kontenerów PHP i PostgreSQL.', 'normal', '2026-01-18'),
(1, 2, 3, 'Widoki SQL', 'Przygotowanie widoków z użyciem JOIN.', 'normal', '2026-01-28'),
(2, 1, 3, 'Funkcja postępu projektu', 'Stworzenie funkcji obliczającej procent ukończenia projektu.', 'normal', '2026-02-01'),
(3, 1, 2, 'Przygotować README', 'Opis uruchomienia, architektury i scenariusza testowego.', 'high', '2026-02-05');

INSERT INTO comments (task_id, user_id, content) VALUES
(1, 1, 'Diagram powinien zawierać relacje 1:1, 1:N i N:N.'),
(2, 2, 'Logowanie będzie oparte o sesję PHP.'),
(3, 1, 'Docker działa poprawnie na porcie 8080.'),
(4, 3, 'Widoki będą używane w panelu dashboard.');

INSERT INTO activity_logs (user_id, task_id, action, description) VALUES
(1, 3, 'TASK_CREATED', 'Utworzono zadanie dotyczące konfiguracji Dockera.'),
(2, 2, 'COMMENT_ADDED', 'Dodano komentarz do zadania logowania użytkownika.');

COMMIT;
# Practical 8: Entity-Relationship (ER) Model & Database Normalization

**Course:** Web Development Frameworks (ITUE203)  
**Database Name:** `studenthub`  

---

## 1. Entity-Relationship Diagram

```mermaid
erDiagram
    USERS ||--o| STUDENTS : "has profile (1:1)"
    USERS ||--o{ AUDIT_LOGS : "performs (1:N)"
    STUDENTS ||--o{ REGISTRATIONS : "makes (1:N)"
    EVENTS ||--o{ REGISTRATIONS : "enrolled in (1:N)"

    USERS {
        int id PK
        string name
        string email UK
        string password_hash
        enum role "student, admin"
        timestamp created_at
    }

    STUDENTS {
        int id PK
        int user_id FK
        string enrollment_no UK
        string name
        string email UK
        string mobile
        string department
        tinyint semester
        decimal gpa
        enum status
    }

    EVENTS {
        int id PK
        string title
        string category
        date event_date
        string location
        int seats
        string poster_image
        enum status
    }

    REGISTRATIONS {
        int id PK
        int student_id FK
        int event_id FK
        timestamp registered_at
        enum status
    }

    AUDIT_LOGS {
        int id PK
        int user_id FK
        string action
        text details
        string ip_address
        timestamp created_at
    }
```

---

## 2. Normalization Analysis (1NF &rarr; 2NF &rarr; 3NF)

1. **First Normal Form (1NF):**
   - Every column contains atomic (indivisible) values. No repeating groups or comma-separated lists of courses or events.
   - Each table defines a distinct primary key (`id`).
2. **Second Normal Form (2NF):**
   - The schema is in 1NF.
   - All non-key attributes are fully functionally dependent on the entire primary key.
   - The Many-to-Many relationship between `students` and `events` is decomposed into the associative entity `registrations` with foreign keys.
3. **Third Normal Form (3NF):**
   - The schema is in 2NF.
   - There are no transitive dependencies (no non-key attribute depends on another non-key attribute).
   - User security credentials reside in `users`, academic records in `students`, event metadata in `events`, preventing update and deletion anomalies.

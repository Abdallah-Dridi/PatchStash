### Fixtures

- `dev_seed.sql`: MySQL dump that rebuilds the schema and inserts demo data (users, projects, modules, assets, patch cycles, vulnerabilities, reports).
    - Load it after creating the empty database: `mysql -u <user> -p patchstash < fixtures/dev_seed.sql`.
    - Demo credentials (password hash is for `admin123`):
        - `admin / admin123` (role: Admin)
        - `pmorris / admin123` (role: ProjectManager)
        - `foperator / admin123` (role: Operator)
    - Regenerate the dump whenever schema/data evolves: `mysqldump -u <user> -p patchstash > fixtures/dev_seed.sql`.

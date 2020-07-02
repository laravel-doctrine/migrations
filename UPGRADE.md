# Upgrade to 2.0

## Breaking Changes
With the move to doctrine/migrations 2.0 you will need to update all your existing migration files  
There is **no** need to re-run you migrations after these changes

### Update `use` Statements
At the top of you migration file update two `use` statements as bellow
- `use Doctrine\Schema\Schema` -> `use Doctrine\DBAL\Schema\Schema`
- `use Doctrine\DBAL\Migrations\AbstractMigration` -> `use Doctrine\Migrations\AbstractMigration`

### Update method declarations
Both the `up` and `down` methods need the `void` return type adding.

Before
```
public function up(Schema $schema)
public function down(Schema $schema)
```
After
```
public function up(Schema $schema): void
public function down(Schema $schema): void
```

<?php

namespace OCA\People\Migration;

use Closure;
use OCP\DB\ISchemaWrapper;
use OCP\Migration\SimpleMigrationStep;
use OCP\Migration\IOutput;

class Version000004Date20200916225555 extends SimpleMigrationStep
{

    /**
     * @param IOutput $output
     * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
     * @param array $options
     * @return null|ISchemaWrapper
     */
    public function changeSchema(IOutput $output, Closure $schemaClosure, array $options)
    {
        /** @var ISchemaWrapper $schema */
        $schema = $schemaClosure();

        if (!$schema->hasTable('people')) {
            $table = $schema->createTable('people');
            $table->addColumn('id', 'integer', [
                'autoincrement' => true,
                'notnull' => true,
            ]);
            $table->addColumn('user_id', 'string', [
                'notnull' => true,
                'length' => 100,
            ]);
            $table->addColumn('name', 'string', [
                'notnull' => true,
                'length' => 100,
            ]);
            $table->addColumn('last_name', 'string', [
                'notnull' => true,
                'length' => 100,
            ]);
            $table->addColumn('work', 'string', [
                'notnull' => true,
                'length' => 100,
            ]);
            $table->addColumn('hobbies', 'string', [
                'notnull' => true,
                'length' => 400,
            ]);
            $table->addColumn('birth', 'string', [
                'notnull' => true,
                'length' => 10,
            ]);
            $table->addColumn('birth_notif', 'integer', [
                'notnull' => true,
                'length' => 1,
            ]);
            $table->addColumn('address', 'string', [
                'notnull' => true,
                'length' => 200,
            ]);
            $table->addColumn('description', 'text', [
                'notnull' => true,
            ]);
            $table->addColumn('updated', 'integer', [
                'notnull' => true,
                'length' => 4,
                'default' => 0,
            ]);

            $table->setPrimaryKey(['id']);
            $table->addUniqueIndex(['user_id', 'name', 'last_name'], 'unique_contact');
        }
        if (!$schema->hasTable('people_tag')) {
            $table = $schema->createTable('people_tag');
            $table->addColumn('id', 'integer', [
                'autoincrement' => true,
                'notnull' => true,
                'unsigned' => true,
            ]);
            $table->addColumn('user_id', 'string', [
                'notnull' => true,
                'length' => 100,
            ]);
            $table->addColumn('name', 'string', [
                'notnull' => false,
                'length' => 100,
            ]);
            $table->addColumn('color', 'string', [
                'notnull' => false,
                'length' => 7,
            ]);
            $table->addColumn('favorite', 'integer', [
                'notnull' => true,
                'length' => 1,
                'default' => 0,
            ]);

            $table->setPrimaryKey(['id']);
            $table->addUniqueIndex(['user_id', 'name'], 'unique_tag');
        }
        if (!$schema->hasTable('people_tag_assign')) {
            $table = $schema->createTable('people_tag_assign');
            $table->addColumn('id', 'integer', [
                'autoincrement' => true,
                'notnull' => true,
                'unsigned' => true,
            ]);
            $table->addColumn('contact_id', 'integer', [
                'notnull' => true,
            ]);
            $table->addColumn('tag_id', 'integer', [
                'notnull' => true,
            ]);

            $table->setPrimaryKey(['id']);
            $table->addUniqueIndex(['contact_id', 'tag_id'], 'unique_tag');
        }
        if (!$schema->hasTable('people_link')) {
            $table = $schema->createTable('people_link');
            $table->addColumn('id', 'integer', [
                'autoincrement' => true,
                'notnull' => true,
                'unsigned' => true,
            ]);
            $table->addColumn('contact_id', 'integer', [
                'notnull' => true,
            ]);
            $table->addColumn('contact_id_bis', 'integer', [
                'notnull' => true,
            ]);
            $table->addColumn('type', 'integer', [
                'notnull' => true,
                'length' => 2,
            ]);
            $table->addColumn('birth', 'string', [
                'notnull' => true,
                'length' => 10,
            ]);
            $table->addColumn('birth_notif', 'integer', [
                'notnull' => true,
                'length' => 1,
            ]);

            $table->setPrimaryKey(['id']);
            $table->addUniqueIndex(['contact_id', 'contact_id_bis'], 'unique_tag');
        }
        return $schema;
    }
}

<?php
	use Cake\Auth\DefaultPasswordHasher;
	use Cake\ORM\TableRegistry;
	use Migrations\AbstractMigration;
	use Phinx\Db\Adapter\MysqlAdapter;

	class Initial extends AbstractMigration {
		public function change() {

			//<editor-fold desc="Související stránky / novinky">
			$table = $this->table('related_contents', ['comment' => 'Související články / novinky ... (číst více)']); //TODO unique
			$table
				->addColumn('source_table_name', 'string', [
					'limit'   => 255,
					'null'    => true,
					'comment' => 'Jméno tabulky ze které se odkazuje'
				])
				->addColumn('target_table_name', 'string', [
					'limit'   => 255,
					'null'    => true,
					'comment' => 'Jméno tabulky na kterou se odkazuje'
				])
				->addColumn('source_table_id', 'integer', [
					'limit'   => 11,
					'null'    => false,
					'comment' => 'ID tabulky ze které se odkazuje'
				])
				->addColumn('target_table_id', 'integer', [
					'limit'   => 11,
					'null'    => false,
					'comment' => 'ID tabulky na kterou se odkazuje'
				])
				->addColumn('created', 'datetime', [
					'null' => false,
				])
				->create();
			/* ░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
			 * ▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓ */
			//</editor-fold>


		}
	}

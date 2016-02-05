<?php
	use Migrations\AbstractMigration;

	class Initial extends AbstractMigration {
		public function change() {

			//<editor-fold desc="Související stránky / novinky">
			$table = $this->table('related_contents_test', ['comment' => 'Související články / novinky ... (číst více)']);
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
				->addIndex(['source_table_name', 'target_table_name', 'source_table_id', 'target_table_id'], ['unique' => true, 'name' => 'unique_binding'])
				->create();
			/* ░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
			 * ▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓ */
			//</editor-fold>


		}
	}

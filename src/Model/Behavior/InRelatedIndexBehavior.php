<?php

	namespace RelatedContent\Model\Behavior;

	use ArrayObject;
	use Cake\Cache\Cache;
	use Cake\Event\Event;
	use Cake\Filesystem\Folder;
	use Cake\ORM\Behavior;
	use Cake\ORM\Entity;
	use Cake\ORM\Table;
	use Cake\ORM\TableRegistry;

	/**
	 * Class HasRelatedBehavior
	 * @package RelatedContent\Model\Behavior
	 *
	 * Init options:
	 *        active_field            - name of field which says if item is active, if present only those with active set as true will be in index
	 *
	 */
	class InRelatedIndexBehavior extends Behavior {

		/**
		 * Vrátí pole s názvy tabulek které mají připnuté chování, po přidání chování je třeba mazat cache
		 *
		 * @return array|mixed
		 */
		static function getTablesWithBehaviorNames() {
			if (($attachedTables = Cache::read('Related.attachedTables')) === false) { //Nepodařilo se načíst z cache
				$attachedTables = self::getAttachedTables();
				$attachedTables = array_map(function ($model) {
					return $model->table();
				}, $attachedTables);
				Cache::write('Related.attachedTables', $attachedTables);
			}

			echo "";

			return $attachedTables;
		}


		public function afterSave(Event $event, Entity $entity, ArrayObject $options) {
			$this->__tableChanged($event, $entity, $options);
		}

		public function afterDelete(Event $event, Entity $entity, ArrayObject $options) {
			$this->__tableChanged($event, $entity, $options);
		}

		/***
		 * Při změně tabulky obnovit index pro tento model
		 *
		 * @param $event
		 * @param $entity
		 * @param $options
		 *
		 * @return bool
		 */
		private function __tableChanged($event, $entity, $options) {
			$modelName = $this->_table->alias();

			if (($indexedTables = Cache::read('Related.indexedTables')) === false) { //Nepodařilo se načíst z cache
				self::refreshCache();
			} else { //Načteno z cache
				$indexedTables[$modelName] = $this->_table->find('list', ['only_active' => true])->toArray();
				Cache::write('Related.indexedTables', $indexedTables);
			}

			return true;
		}

		static function refreshCache() {
			$indexedTables = self::getListForAllAttachedModels();
			Cache::write('Related.indexedTables', $indexedTables);
		}

		/***
		 * Najde všechny modely které mají připnuté tohle chování a přegeneruje jim index
		 */
		static function getListForAllAttachedModels() {
			$indexed_tables     = [];
			$tablesWithBehavior = self::getAttachedTables();

			/** @var Table $table */
			foreach ($tablesWithBehavior as $table) {
				$indexed_tables[$table->alias()] = $table->find('list', ['only_active' => true])->toArray();
			}

			return $indexed_tables;
		}

		/**
		 * Vrátí tabulky s chováním, které se mají indexovat
		 *
		 *
		 * @param bool       $indexedByName Zda li má vrátit jména místo tabulek
		 *
		 * @return array
		 */
		static function getAttachedTables($indexedByName = false) {

			$modelPath = 'Model' . DS . 'Table';
			$modelPath = APP . $modelPath;

			$tables = [];
			foreach ((new Folder($modelPath))->find('.*.php') as $file) {
				$table = str_replace('Table.php', '', $file);
				if (TableRegistry::exists($table)) {
					TableRegistry::remove($table);
				}
				$tableTable = TableRegistry::get($table, ['options' => ['skipSimilarInitialize' => true]]);

				if ($tableTable->hasBehavior('InRelatedIndex')) {
					if ($indexedByName) {
						$tables[$table] = $tableTable;
					} else {
						$tables[] = $tableTable;
					}
				}
			}

			return $tables;
		}

		/*static function getRelatedArray() {
			$relatedTablesArray = self::getTablesWithBehaviorNames();
			return array_map(function($name){return 'Related' . Inflector::camelize($name);}, $relatedTablesArray);
		}*/
	}

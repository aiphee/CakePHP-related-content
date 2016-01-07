<?php

	namespace SimilarContent\Model\Behavior;

	use ArrayObject;
	use Cake\Cache\Cache;
	use Cake\Event\Event;
	use Cake\Filesystem\Folder;
	use Cake\I18n\Time;
	use Cake\ORM\Behavior;
	use Cake\ORM\Entity;
	use Cake\ORM\Query;
	use Cake\ORM\Table;
	use Cake\ORM\TableRegistry;
	use Cake\Utility\Inflector;

	class HasSimilarBehavior extends Behavior {
		/**
		 * Vrátí pole s názvy tabulek které mají připnuté chování, po přidání chování je třeba mazat cache
		 *
		 * Inicializace:
		 * 		$this->addBehavior('SimilarContent.HasSimilar', isset($config['options']) ? $config['options'] : []);
		 * Inicializace tabulky na kterou ostatní nemůžou odkazovat:
		 *		$this->addBehavior('SimilarContent.HasSimilar', isset($config['options']) ? array_merge($config['options'], ['in_index' => false]) : ['in_index' => false]);
		 *
		 * @param bool $thisTable
		 *
		 * @return array|mixed
		 */
		static function getTablesWithBehaviorNames($thisTable = false) {
			if (($attachedTables = Cache::read('Similar.attachedTables')) === false) { //Nepodařilo se načíst z cache
				$attachedTables = self::getAttachedTables($thisTable);
				$attachedTables = array_map(function ($model) {
					return $model->table();
				}, $attachedTables);
				Cache::write('Similar.attachedTables', $attachedTables);
			}

			echo "";

			return $attachedTables;
		}

		public function initialize(array $config) {
			if (!isset($config['skipSimilarInitialize']) || !$config['skipSimilarInitialize']) {
				$attachedTables = self::getTablesWithBehaviorNames($this->_table);

				/** @var Table $attachedTable */
				foreach ($attachedTables as $tableName) {

					$modelName = Inflector::camelize($tableName);

					$options = [
						'className'        => $modelName,
						'through'          => 'RelatedContents',
						'foreignKey'       => 'source_table_id',
						'targetForeignKey' => 'target_table_id',
						'propertyName'     => 'related-' . $tableName,
//						'saveStrategy'     => 'replace',
						'dependent'        => true,
						'conditions'       => [
							'RelatedContents.source_table_name' => $this->_table->table(),
							'RelatedContents.target_table_name' => $tableName,
						]
					];
					$this->_table->belongsToMany('Related' . $modelName, $options);
				}
			}
		}


		public function beforeFind(Event $event, Query $query, ArrayObject $options, $primary) {
			if (!array_key_exists('getRelated', $options) || !$options['getRelated']) { //Jen pokud se mají related stahovat
				return true;
			}
			$attachedTables = self::getTablesWithBehaviorNames();

			/** @var Table $attachedTable */
			foreach ($attachedTables as $tableName) {
				$modelName = Inflector::camelize($tableName);
				$query->contain(['Related' . $modelName => []]);
			}


			$query->formatResults(function ($results) {
				return $results->map(

					function ($row) {
						$temp = $row->toArray();

						$related = [];
						foreach ($temp as $key => $item) {
							if (preg_match('/related-.*/', $key)) {

								foreach ($row->{$key} as $id => $similar) {
									$table_name                   = explode('-', $key);
									$row->{$key}[$id]->table_name = end($table_name);
								}

								$related = array_merge($related, $row->{$key});
								unset($row->{$key});
							}
						}

						$row->related = $related;

						return $row;
					});
			});

			return true;
		}

		public function beforeMarshal(Event $event, ArrayObject $data, ArrayObject $options) {
			if (count($data) > 5) {
				foreach ($data as $key => &$item) {
					if (is_array($item) && preg_match('/related-.*/', $key)) {
						foreach ($item as &$related) {
							$related['_joinData']['created'] = Time::now();
						}
					}
				}

//				unset($data['related']);

				echo ""; //TODO $article->dirty('comments', true); aby se neaktlizovalo id?
			}

			/*if (isset($data['related']) > 0) {
				$relatedContentsTable = TableRegistry::get('RelatedContents');
				foreach ($data['related'] as $id => &$related) {
					$related['source_table_name'] = $this->_table->table();
					$related['created']           = Time::now();
				}
				$entities = $relatedContentsTable->newEntities($data['related']);

				$relatedContentsTable->connection()->transactional(function () use ($relatedContentsTable, $entities, $data) {
					$relatedContentsTable->deleteAll([
						'source_table_name' => $this->_table->table(),
						'source_table_id'   => reset($data['related'])['source_table_id'],
					]);
					foreach ($entities as $entity) {
						$relatedContentsTable->save($entity, ['atomic' => false]);
					}
				});

			}*/
		}

		public function beforeSave($event, $entity, $options) {
			if (isset($entity->related)) { //TODO, zbytečně nastavuje všechny na dirty
				foreach (self::getTablesWithBehaviorNames() as $tablesWithBehaviorName) {
					$entity->dirty('related-' . $tablesWithBehaviorName, true);
				}

			}

			return true;
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
			$config = $this->_table->behaviors()->HasSimilar->config();
			if(isset($config['in_index']) && $config['in_index'] === false) {
				return true;
			}
			$modelName = $this->_table->alias();

			if (($indexedTables = Cache::read('Similar.indexedTables')) === false) { //Nepodařilo se načíst z cache
				self::refreshCache();
			} else { //Načteno z cache
				$indexedTables[$modelName] = $this->_table->find('list')->toArray();
				Cache::write('Similar.indexedTables', $indexedTables);
			}
			return true;
		}

		static function refreshCache() {
			$indexedTables = self::getListForAllAttachedModels();
			Cache::write('Similar.indexedTables', $indexedTables);
		}

		/***
		 * Najde všechny modely které mají připnuté tohle chování a přegeneruje jim index
		 */
		static function getListForAllAttachedModels() {
			$indexed_tables     = [];
			$tablesWithBehavior = self::getAttachedTables();

			/** @var Table $table */
			foreach ($tablesWithBehavior as $table) {
				$indexed_tables[$table->alias()] = $table->find('list')->toArray();
			}

			return $indexed_tables;
		}

		/**
		 * Vrátí tabulky s chováním, které se mají indexovat
		 *
		 * @param Table|bool $thisTable
		 *
		 * @param bool       $indexedByName Zda li má vrátit jména místo tabulek
		 *
		 * @return array
		 */
		static function getAttachedTables($thisTable = false, $indexedByName = false) {
			TableRegistry::clear(); //TODO špatně špatně špatně, Hlásí že tabulka už je v registrech, ale kde?

			$modelPath = 'Model' . DS . 'Table';
			$modelPath = APP . $modelPath;

			$tables = [];
			foreach ((new Folder($modelPath))->find('.*.php') as $file) {
				$table = str_replace('Table.php', '', $file);
				if ($thisTable && $thisTable->alias() === $table) {
					$tables[] = $thisTable;
				} else {
					$tableTable = TableRegistry::get($table, ['options' => ['skipSimilarInitialize' => true]]);


					if ($tableTable->hasBehavior('HasSimilar')) {
						$config = $tableTable->behaviors()->HasSimilar->config();
						if (!isset($config['in_index']) || $config['in_index'] == true) {
							if ($indexedByName) {
								$tables[$table] = $tableTable;
							} else {
								$tables[] = $tableTable;
							}
						}
					}

				}
			}

			TableRegistry::clear(); //TODO špatně špatně špatně, Hlásí že tabulka už je v registrech, ale kde?
			return $tables;
		}


		/*static function getRelatedArray() {
			$relatedTablesArray = self::getTablesWithBehaviorNames();
			return array_map(function($name){return 'Related' . Inflector::camelize($name);}, $relatedTablesArray);
		}*/
	}
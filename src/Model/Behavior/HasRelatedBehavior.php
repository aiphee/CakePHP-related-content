<?php

	namespace RelatedContent\Model\Behavior;

	use ArrayObject;
	use Cake\Event\Event;
	use Cake\I18n\Time;
	use Cake\ORM\Behavior;
	use Cake\ORM\Query;
	use Cake\ORM\Table;
	use Cake\Utility\Inflector;

	/**
	 * Class HasRelatedBehavior
	 * @package RelatedContent\Model\Behavior
	 *
	 *
	 * Inicializace:
	 *        $this->addBehavior('RelatedContent.HasRelated', isset($config['options']) ? $config['options'] : []);
	 * Search options:
	 *        getRelated                - if present, get related in search, otherwise it wont fetch
	 */
	class HasRelatedBehavior extends Behavior {

		public function initialize(array $config) {

			if (!$this->config('skipSimilarInitialize')) {
				$attachedTables = InRelatedIndexBehavior::getTablesWithBehaviorNames();

				/** @var \Cake\ORM\Table $attachedTable */
				foreach ($attachedTables as $tableName) {

					$modelName = Inflector::camelize($tableName);

					$options = [
						'className' => $modelName,
						'through' => 'RelatedContents',
						'foreignKey' => 'source_table_id',
						'targetForeignKey' => 'target_table_id',
						'propertyName' => 'related-' . $tableName,
						'dependent' => true,
						'conditions' => [
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
			$attachedTables = InRelatedIndexBehavior::getTablesWithBehaviorNames();

			/** @var \Cake\ORM\Table $attachedTable */
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
									$table_name = explode('-', $key);
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
			if (count($data) > 5) { //TODO Make this less magical
				foreach ($data as $key => &$item) {
					if (is_array($item) && preg_match('/related-.*/', $key)) {
						foreach ($item as &$related) {
							$related['_joinData']['created'] = Time::now();
						}
					}
				}
			}
		}

		public function beforeSave($event, $entity, $options) {
			if (isset($entity->related)) { //TODO, related are being marked as dirty even when they are not
				foreach (InRelatedIndexBehavior::getTablesWithBehaviorNames() as $tablesWithBehaviorName) {
					$entity->dirty('related-' . $tablesWithBehaviorName, true);
				}

			}

			return true;
		}


	}

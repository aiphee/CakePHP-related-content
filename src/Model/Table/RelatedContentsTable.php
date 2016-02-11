<?php
namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * RelatedContents Model
 *
 * @property \Cake\ORM\Association\BelongsTo $SourceTables
 * @property \Cake\ORM\Association\BelongsTo $TargetTables
 */
class RelatedContentsTable extends Table {

	/**
	 * Initialize method
	 *
	 * @param array $config The configuration for the Table.
	 *
	 * @return void
	 */
	public function initialize(array $config) {
		parent::initialize($config);

		$this->table('related_contents');
		$this->displayField('id');
		$this->primaryKey('id');

		$this->addBehavior('Timestamp');
	}

	/**
	 * Default validation rules.
	 *
	 * @param \Cake\Validation\Validator $validator Validator instance.
	 *
	 * @return \Cake\Validation\Validator
	 */
	public function validationDefault(Validator $validator) {
		$validator
			->add('id', 'valid', ['rule' => 'numeric'])
			->allowEmpty('id', 'create');

		$validator
			->allowEmpty('source_table_name');

		$validator
			->allowEmpty('target_table_name');

		return $validator;
	}
}

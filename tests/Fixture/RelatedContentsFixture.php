<?php
namespace RelatedContent\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class RelatedContentsFixture extends TestFixture {

	/**
	 * Fields
	 *
	 * @var array
	 */
	public $fields = [
		'id'                => ['type' => 'integer', 'null' => false, 'autoIncrement' => true],
		'source_table_name' => ['type' => 'string', 'null' => false],
		'target_table_name' => ['type' => 'string', 'null' => false],
		'source_table_id'   => ['type' => 'integer', 'null' => false],
		'target_table_id'   => ['type' => 'integer', 'null' => false],
		'_constraints'      => [
			'primary' => ['type' => 'primary', 'columns' => ['id']]
		]
	];

	/**
	 * Records
	 *
	 * @var array
	 */
	public $records = [
		[
			'id'                => 1,
			'source_table_name' => 'articles',
			'target_table_name' => 'articles',
			'source_table_id'   => 1,
			'target_table_id'   => 2,
		],
		[
			'id'                => 2,
			'source_table_name' => 'articles',
			'target_table_name' => 'articles',
			'source_table_id'   => 1,
			'target_table_id'   => 4,
		],
	];
}

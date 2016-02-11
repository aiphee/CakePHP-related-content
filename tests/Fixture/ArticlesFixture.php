<?php
namespace RelatedContent\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class ArticlesFixture extends TestFixture {

	/**
	 * Fields
	 *
	 * @var array
	 */
	public $fields = [
		'id'           => ['type' => 'integer', 'null' => false, 'autoIncrement' => true],
		'title'        => ['type' => 'string', 'null' => false, 'default' => null],
		'_constraints' => [
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
			'id'    => 1,
			'title' => 'Test title one'
		],
		[
			'id'    => 2,
			'title' => 'Test title two'
		],
		[
			'id'    => 3,
			'title' => 'Test title three'
		],
		[
			'id'    => 4,
			'title' => 'Test title four'
		],
	];
}

<?php
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\TestSuite\IntegrationTestCase;

/**
 * Created by PhpStorm.
 * User: jirka
 * Date: 11.2.16
 * Time: 14:01
 */
class RelatedContentControllerTest extends IntegrationTestCase {

	public function setUp() {
		parent::setUp();
	}

	public function tearDown() {
		parent::tearDown();
		TableRegistry::clear();
	}


	public function testSearch() {
		$this->post(['plugin' => 'RelatedContent', 'controller' => 'RelatedContent', 'action' => 'search'], ['term' => 'Test title three']);
		$this->assertResponseEquals(json_encode([
			[
				'key'   => 3,
				'value' => 'Test title thr',
				'model' => 'Articles',
				'table' => 'articles'
			]
		]));
	}

	public function getActionName() {
		$this->markTestIncomplete('Finish after testSearch works');
	}
}

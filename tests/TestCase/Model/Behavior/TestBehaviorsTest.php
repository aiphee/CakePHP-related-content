<?php
namespace RelatedContent\Test\TestCase\Model\Behavior;


use Cake\Cache\Cache;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Test\Fixture\ArticlesFixture;
use Cake\TestSuite\TestCase;
use RelatedContent\Model\Behavior\HasRelatedBehavior;
use RelatedContent\Model\Behavior\InRelatedIndexBehavior;


/**
 * Class TestBehaviorsTest
 * @package RelatedContent\Test\TestCase\Model\Behavior
 * @property Table $Articles
 */
class TestBehaviorsTest extends TestCase {

	public $fixtures = [
		'plugin.RelatedContent.Articles',
		'plugin.RelatedContent.RelatedContents'
	];

	/**
	 * setup
	 *
	 * @return void
	 */
	public function setUp() {
		parent::setUp();

		TableRegistry::clear();
		$this->Articles = TableRegistry::get('Articles');

		Cache::config('default', [
			'className' => 'File',
			'path'      => CACHE,
			'duration'  => '+10 days',
		]);

		Cache::delete('Related.attachedTables');
		Cache::delete('Related.indexedTables');
	}


	public function testInIndexBehavior() {
		$this->Articles->addBehavior('RelatedContent\Test\TestCase\Model\Behavior\TestInRelatedIndexBehavior');
		$this->Articles->addBehavior('RelatedContent\Test\TestCase\Model\Behavior\TestHasRelatedBehavior');

		/*$testEntity = $this->Articles->newEntity(['id' => 5, 'title' => 'Test title five']);
		$this->Articles->save($testEntity);*/

		//<editor-fold desc="Should get two related">
		$firstArticle = $this->Articles->get(1, ['getRelated' => true]);

		$this->assertCount(2, $firstArticle->related);
		//</editor-fold>

		//<editor-fold desc="Should save only one">
		$firstArticle = $this->Articles->patchEntity($firstArticle, [
			'related-articles' => [
				[
					'id'        => 3,
					'_joinData' => [
						'source_table_name' => 'articles',
						'target_table_name' => 'articles'
					]
				]
			]
		]);

		$this->Articles->save($firstArticle);

		$firstArticle = $this->Articles->get(1, ['getRelated' => true]);
		$this->assertCount(1, $firstArticle->related);
		//</editor-fold>

		//<editor-fold desc="Test if cache works">
		$newArticle = $this->Articles->newEntity(['id' => 5, 'title' => 'Test title five']);
		$this->Articles->save($newArticle);


		$attachedTables = Cache::read('Related.attachedTables');
		$indexedTables  = Cache::read('Related.indexedTables');

		$this->assertCount(1, $attachedTables);
		$this->assertEquals('articles', $attachedTables[0]);

		$this->assertCount(5, $indexedTables['Articles']);
		$this->assertEquals('Test title five', $indexedTables['Articles'][5]);

		$this->Articles->delete($this->Articles->get(3));
		$indexedTables = Cache::read('Related.indexedTables');
		$this->assertCount(4, $indexedTables['Articles']);
		//</editor-fold>
	}
}

class TestInRelatedIndexBehavior extends InRelatedIndexBehavior {

	public function getAttachedTables($indexedByName = false) {
		return $indexedByName ? 'Articles' : [TableRegistry::get('Articles')];
	}
}


class TestHasRelatedBehavior extends HasRelatedBehavior {
	protected function _getInRelated() {
		return new TestInRelatedIndexBehavior($this->_table);
	}
}




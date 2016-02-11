<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * RelatedContent Entity.
 *
 * @property int             $id
 * @property string          $source_table_name
 * @property string          $target_table_name
 * @property int             $source_table_id
 * @property int             $target_table_id
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 */
class RelatedContent extends Entity {

	/**
	 * Fields that can be mass assigned using newEntity() or patchEntity().
	 *
	 * Note that when '*' is set to true, this allows all unspecified fields to
	 * be mass assigned. For security purposes, it is advised to set '*' to false
	 * (or remove it), and explicitly make individual fields accessible as needed.
	 *
	 * @var array
	 */
	protected $_accessible = [
		'*' => true,
		'id' => false,
	];
}

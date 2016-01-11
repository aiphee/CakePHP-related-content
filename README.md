# SimilarContent plugin for CakePHP 3

![alt text](/related1.png "Logo Title Text 1")
![alt text](/related2.png "Logo Title Text 1")


This is a fast made plugin made for simple maintaining of relationships.

You can define that one entry in table has similar entries in other tables.

It also has basic element with ajax which relies on `Bootstrap 3`.

Please, be noted that this is not a complex plugin, there may be serious bugs, you are welcome to report or repair them.

## Installation 

 - Run migration from plugin or create table manually.
```SQL
CREATE TABLE `related_contents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `source_table_name` varchar(255) DEFAULT NULL COMMENT 'Jméno tabulky ze které se odkazuje',
  `target_table_name` varchar(255) DEFAULT NULL COMMENT 'Jméno tabulky na kterou se odkazuje',
  `source_table_id` int(11) NOT NULL COMMENT 'ID tabulky ze které se odkazuje',
  `target_table_id` int(11) NOT NULL COMMENT 'ID tabulky na kterou se odkazuje',
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`)
)
```

 - Copy to plugins directory
 
 - Add to your `bootstrap.php`
```PHP
Plugin::load('SimilarContent', ['routes' => true]);
```
 - Add to your cakephp composer.json
```Composer
    "autoload": {
        "psr-4": {
            "App\\": "src",
            "SimilarContent\\": "./plugins/SimilarContent/src",
        }
    }
```

## Usage
 - add behavior to your table to be able to search or be found
```PHP
$this->addBehavior('SimilarContent.HasSimilar', isset($config['options']) ? $config['options'] : []);
```
If you dont want table in index, use this as a second parameter:
```isset($config['options']) ? array_merge($config['options'], ['in_index' => false]) : ['in_index' => false]```
 - Add element to your view
```PHP
<?= $this->element('SimilarContent.managingRelated', ['tables_to_get' => ['ContentNews', 'ContentPages']]) ?>
```
Parameter `tables_to_get` is optional, it will allow to search just in some tables.

 - To get related content with your entry, you have to pass parameter to find
```PHP
$entity = $this->ContentPages->get($id, [
				'getRelated' => true
			]);
```



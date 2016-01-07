# SimilarContent plugin for CakePHP

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


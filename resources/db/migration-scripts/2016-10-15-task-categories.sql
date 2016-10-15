START TRANSACTION;

CREATE TABLE `task_category` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(255) NOT NULL
) ENGINE='InnoDB';

INSERT INTO `task_category` (`id`, `name`) VALUES
(1,	'Highest'),
(2,	'High'),
(3,	'Medium'),
(4,	'Low'),
(5,	'Lowest');

ALTER TABLE `task`
ADD `task_category_id` int(11) NULL AFTER `task_group_id`,
ADD FOREIGN KEY (`task_category_id`) REFERENCES `task_category` (`id`) ON DELETE RESTRICT;

COMMIT;

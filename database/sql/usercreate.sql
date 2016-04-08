CREATE TABLE `acket`.`users`( 
	`id` SERIAL NOT NULL, 
	name VARCHAR(100) NOT NULL,
	`password` VARCHAR(100) NOT NULL, 
	`remember_token` VARCHAR(100) NULL, 
	`email` VARCHAR(100) NOT NULL, 
	`created_at` TIMESTAMP NOT NULL DEFAULT now(), 
	`updated_at` TIMESTAMP NOT NULL DEFAULT now(), 
	`description` VARCHAR(256) DEFAULT 'Description not available.', 
	PRIMARY KEY (`id`)
);
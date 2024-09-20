USE `SGU_E_Commerce`;


-- Insert sample data into UserInformation table
INSERT INTO `UserInformation` 	(`Id`, 		`Email`, 							`Address`, 			`Birthday`, 		`Fullname`, 		`Gender`,		 `PhoneNumber`) VALUES
								(1, 		'admin@gmail.com', 					'123 Main St', 		'2004-04-01', 		'Ngô Tuấn Hưng', 	'Male', 		'123-456-7890'),
								(2, 		'nguyenphucminh880@gmail.com', 		'456 Elm St', 		'2004-11-15', 		'Nguyễn Minh Phúc', 'Male', 		'234-567-8901'),
								(3, 		'hungnt.020404@gmail.com', 			'123 Main St', 		'2004-04-01', 		'Ngô Tuấn Hưng', 	'Male', 		'123-456-7890'),
								(4, 		'admin004@gmail.com', 				'123 Main St', 		'2004-04-01', 		'Mr 004', 			'Male', 		'123-456-7890'),
								(5, 		'admin005@gmail.com', 				'123 Main St', 		'2004-04-01', 		'Mr 005', 			'Male', 		'123-456-7890'),
								(6, 		'user006@gmail.com', 				'123 Main St', 		'2004-04-01', 		'Mr 006', 			'Male', 		'123-456-7890'),
								(7, 		'user007@gmail.com', 				'123 Main St', 		'2004-04-01', 		'Mr 007', 			'Male', 		'123-456-7890'),
								(8, 		'user008@gmail.com', 				'123 Main St', 		'2004-04-01', 		'Mr 008', 			'Male', 		'123-456-7890'),
								(9, 		'user009@gmail.com', 				'123 Main St', 		'2004-04-01', 		'Mr 009', 			'Male', 		'123-456-7890'),
								(10, 		'user010@gmail.com', 				'123 Main St', 		'2004-04-01', 		'Mr 010', 			'Male', 		'123-456-7890');


                        
                        -- Insert sample data into Account table
INSERT INTO `Account` 	(`Id`,			`Password`,														 `Status`, 		`Role`,		`UserInformationId`,		`CreateTime`, 			`Active`) VALUES
						(1,				'$2a$10$W2neF9.6Agi6kAKVq8q3fec5dHW8KUA.b0VSIGdIZyUravfLpyIFi', 	1, 			'Admin',					1,			'2023-01-01 00:00:00',			1),
						(2,				'$2a$10$W2neF9.6Agi6kAKVq8q3fec5dHW8KUA.b0VSIGdIZyUravfLpyIFi', 	0, 			'User',						2,			'2024-01-01 00:00:00',  		1),
						(3,				'$2a$10$W2neF9.6Agi6kAKVq8q3fec5dHW8KUA.b0VSIGdIZyUravfLpyIFi', 	1, 			'User',						3,			'2024-01-01 00:00:00',  		1),
						(4,				'$2a$10$W2neF9.6Agi6kAKVq8q3fec5dHW8KUA.b0VSIGdIZyUravfLpyIFi', 	1, 			'Admin',					4,			'2024-01-01 00:00:00',  		1),
						(5,				'$2a$10$W2neF9.6Agi6kAKVq8q3fec5dHW8KUA.b0VSIGdIZyUravfLpyIFi', 	1, 			'Admin',					5,			'2024-01-01 00:00:00',  		1),
						(6,				'$2a$10$W2neF9.6Agi6kAKVq8q3fec5dHW8KUA.b0VSIGdIZyUravfLpyIFi', 	1, 			'User',						6,			'2024-01-01 00:00:00',  		1),
						(7,				'$2a$10$W2neF9.6Agi6kAKVq8q3fec5dHW8KUA.b0VSIGdIZyUravfLpyIFi', 	1, 			'User',						7,			'2024-01-01 00:00:00',  		1),
						(8,				'$2a$10$W2neF9.6Agi6kAKVq8q3fec5dHW8KUA.b0VSIGdIZyUravfLpyIFi', 	1, 			'User',						8,			'2024-01-01 00:00:00',  		1),
						(9,				'$2a$10$W2neF9.6Agi6kAKVq8q3fec5dHW8KUA.b0VSIGdIZyUravfLpyIFi', 	1, 			'User',						9,			'2024-01-01 00:00:00',  		1),
						(10,			'$2a$10$W2neF9.6Agi6kAKVq8q3fec5dHW8KUA.b0VSIGdIZyUravfLpyIFi', 	1, 			'User',						10,			'2024-01-01 00:00:00',  		1);


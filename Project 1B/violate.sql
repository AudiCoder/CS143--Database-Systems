-- Violates Check Constraint of `Movie` table  
-- inserting a tuple having `id` less than zero.
INSERT INTO Movie VALUES (-23, 'ABC',2002, 'R', 'Universal Studios');

-- violates Primary Key Constraint of `Movie` table 
-- inserting a tuple having `id` that is already present in table
INSERT INTO Movie VALUES (22, 'BCD',20, 'R','univsersal Studios');
--ERROR 1062 (23000): Duplicate entry '22' for key 1

-- Movie title Cannot be NULL
INSERT INTO Movie  VALUES (4800, NULL,2014, NULL, "PP");
--ERROR 1048 (23000): Column 'title' cannot be null

--Primary Key cannot be NULL
INSERT INTO Movie VALUES (NULL, 'BCD',20, 'R','univsersal Studios');
--ERROR 1048 (23000): Column 'id' cannot be null


-- violates Primary Key Constraint of `Actor` table 
-- inserting a tuple having `id` that is already present in table
INSERT INTO Actor VALUES (60, 'mmmm', 'nnnn', NULL, '1978-01-25' , NULL);
--ERROR 1062 (23000): Duplicate entry '60' for key 1

-- violates Check Constraint of `Actor` table by 
-- inserting a tuple with dob greater than dod
INSERT INTO Actor VALUES (24555,'mmmmm','nnnn', NULL, '1978-01-25','1974-01-25');



--In Actor table NAme Cannot be NULL
INSERT INTO Actor VALUES (24555, 'mmmm', NULL , NULL, '1978-01-25' , NULL);
--ERROR 1048 (23000): Column 'first' cannot be null

--DOb cannot be NULL in Actor table
INSERT INTO Actor VALUES (24555, 'mmmm', 'nnnn', NULL, NULL , NULL);
--ERROR 1048 (23000): Column 'dob' cannot be null


-- violates Primary Key Constraint of `Director` table 
-- inserting a tuple having `id` that is already present in table
INSERT INTO Director VALUES (22, 'mmmm', 'nnnn', '1978-01-25' , NULL);
--ERROR 1062 (23000): Duplicate entry '22' for key 1

-- violates Check Constraint of `Director` table by 
-- inserting a tuple with dob greater than dod
INSERT INTO Director VALUES (24555,'mmmm','nnnn', '1978-01-25','1974-01-25');


--In Director table NAme Cannot be NULL
INSERT INTO Director VALUES (24555, 'mmmm', NULL , '1978-01-25' , NULL);
--ERROR 1048 (23000): Column 'first' cannot be null

--DOb cannot be NULL in Director table
INSERT INTO Director VALUES (24555, 'mmmm', 'nnnn', NULL , NULL);
--ERROR 1048 (23000): Column 'dob' cannot be null



-- Violates Primary Key Constraint of `MovieGenre` table
-- tuple have same (`mid`, `genre`) that is already present in database.
INSERT INTO MovieGenre VALUES (2,'Comedy');
--ERROR 1062 (23000): Duplicate entry '2-Comedy' for key 1


-- violates Foreign Key Constraint of `MovieGenre` table
-- `mid` not present in the `Movie` table
INSERT INTO MovieGenre VALUES (6432,'violate foreign key');
--ERROR 1452 (23000): Cannot add or update a child row: a foreign key constraint fails (`TEST/MovieGenre`, CONSTRAINT `MovieGenre_ibfk_1` FOREIGN KEY (`mid`) REFERENCES `Movie` (`id`) ON DELETE CASCADE)

-- Violates Primary Key Constraint of `MovieDirector` table
-- tuple have same (`mid`, `did`) that is already present in database.
INSERT INTO MovieDirector VALUES (844,68622);
--ERROR 1062 (23000): Duplicate entry '844-68622' for key 1

-- violates Foreign Key Constraint of `MovieDirector` table
-- `mid` not present in the `Movie` table
INSERT INTO MovieDirector VALUES (60000,158);
--ERROR 1452 (23000): Cannot add or update a child row: a foreign key constraint fails (`TEST/MovieDirector`, CONSTRAINT `MovieDirector_ibfk_1` FOREIGN KEY (`mid`) REFERENCES `Movie` (`id`) ON DELETE CASCADE)



-- Violates Primary Key Constraint of `MovieActor` table
-- tuple have same (`mid`, `aid`) that is already present in database.
INSERT INTO MovieActor VALUES (4732,2585,'asds');
--ERROR 1062 (23000): Duplicate entry '4732-2585' for key 1

-- Violates Primary Key Constraint of `MovieActor` table
-- aid cannot be NULL
INSERT INTO MovieActor VALUES (4732,NULL,'asds');
--ERROR 1048 (23000): Column 'aid' cannot be null

-- violates Foreign Key Constraint of `MovieActor` table
-- `mid` not present in the `Movie` table
INSERT INTO MovieActor VALUES (4731,158,'asdf');
--ERROR 1452 (23000): Cannot add or update a child row: a foreign key constraint fails (`TEST/MovieActor`, CONSTRAINT `MovieActor_ibfk_1` FOREIGN KEY (`mid`) REFERENCES `Movie` (`id`) ON DELETE CASCADE)


-- violates Foreign Key Constraint of `MovieActor` table 
-- `aid` not present in the `Actor` table
INSERT INTO MovieActor VALUES (4732,788888, NULL);
--ERROR 1452 (23000): Cannot add or update a child row: a foreign key constraint fails (`TEST/MovieActor`, CONSTRAINT `MovieActor_ibfk_2` FOREIGN KEY (`aid`) REFERENCES `Actor` (`id`) ON DELETE CASCADE)


-- violates CHECK constraint of `Review` table 
-- inserting a tuple having `rating` negative
INSERT INTO Review VALUES ('Test User', '2014-01-25 03:14:07', 3, -22, NULL);



-- violates Primary Key Constraint of `MaxMovieID` table
-- `id` cannot be NULL.
INSERT INTO MaxMovieID VALUES (NULL);
ERROR 1048 (23000): Column 'id' cannot be null



-- violates CHECK Constraint of `MaxPersonID` table by
-- 'id' should be greater than 0
INSERT INTO MaxPersonID VALUES (-33);



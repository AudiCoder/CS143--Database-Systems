--Names of all the actors in the movie 'Die Another Day'.
SELECT CONCAT(first, " ", last) AS `Actor Name`
FROM Actor A, Movie M, MovieActor MA
WHERE M.id = MA.mid AND MA.aid = A.id AND M.title = 'Die Another Day';


-- Count of all the actors who acted in multiple movies
select count(*) as 'Count Value of actors with multiple movies' from (select count(*) from MovieActor MA group by MA.aid having count(*)>1) new_tbl; 

-- Count of movies relesed in year 2000 with rating R
select count(*) from Movie where year= 2000 and rating='R';

-- Names of people who are Actor as well as director and are dead
Select concat(A.first, " ", A.last)
from Actor A, Director D
where A.dod<>'NULL' AND A.id=D.id;

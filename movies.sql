DROP TABLE IF EXISTS Movies;

CREATE TABLE Movies
(
	id INT(7) UNSIGNED NOT NULL AUTO_INCREMENT,
    rank INT(7) UNSIGNED NOT NULL,
    rating DECIMAL(3,1) UNSIGNED NOT NULL,
	title VARCHAR(255) NOT NULL,
	release_year YEAR(4) NOT NULL,
	number_of_votes INT(10) UNSIGNED NOT NULL,
	CONSTRAINT PK_Movie_id PRIMARY KEY (id)
);

ALTER TABLE Movies
    ADD CONSTRAINT Unique_Rank UNIQUE (rank);

CREATE INDEX idx_release_year ON Movies (release_year);
CREATE INDEX idx_number_of_votes ON Movies (number_of_votes);

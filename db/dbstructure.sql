
CREATE TABLE Genres (
	ID VARCHAR(16) PRIMARY KEY,
	Name TEXT NOT NULL
)

CREATE TABLE Videos (
	ID INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	Status INT(1),
	Rating INT(1),
	Title TEXT,
	OrigTitle TEXT,
	OtherTitles TEXT,
	Predecessor INT UNSIGNED,
	Successor INT UNSIGNED,
	Enlisted TIMESTAMP,
	Country VARCHAR(16),
	Genre VARCHAR(16),
	Lang CHAR(2),
	Year INT(4),
	Duration INT(4),
	Medium VARCHAR(16),
	Resolution VARCHAR(16),
	Cut BOOLEAN NOT NULL DEFAULT FALSE,
	File TEXT,
	Location TEXT,
	Info TEXT,
	Link TEXT,
	Director VARCHAR(64),
	Actors VARCHAR(1024),
	Watched TIMESTAMP,
	WatchCnt INT
)

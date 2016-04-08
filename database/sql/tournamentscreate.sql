CREATE TABLE tournaments(
    id SERIAL PRIMARY KEY,
    name varchar(100) NOT NULL,
	status int DEFAULT 0,
	isHidden bool DEFAULT false,
	gamesPlayed int DEFAULT 0,
    description varchar(4000) DEFAULT null,
	hostId INTEGER REFERENCES users(id),
	participantList text NOT NULL,
    start_date TIMESTAMP NULL DEFAULT now(),
    end_date TIMESTAMP NULL DEFAULT now(),
    posted_date TIMESTAMP DEFAULT now(),
	match_update_date TIMESTAMP DEFAULT now(),
	description_update_date TIMESTAMP DEFAULT now(),
    brackets text NOT NULL,
    tags text
    
);
CREATE TABLE participants(
    tournamentId integer REFERENCES tournaments(id),
    participantId integer NOT NULL,
	name varchar(100) NOT NULL,
	tournamentStatus int DEFAULT 0,
	gameStatus int DEFAULT 0,
	depthId int DEFAULT 0,
	gameId int DEFAULT 0,
	participantSide int DEFAULT 0,
	isHidden bool DEFAULT false,
	wins int DEFAULT 0,
    PRIMARY KEY (tournamentId, participantId)
);
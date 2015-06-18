create table tweets (
	id varchar(32),
	username varchar(500),
	tweetid bigint,
	tweet varchar(2000),
	ts timestamp with time zone,
	class varchar(200),
	accessedsurvey timestamp with time zone
);
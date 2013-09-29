BEGIN;
CREATE TABLE "topic" (
    "id" serial NOT NULL PRIMARY KEY,
    "forum_id" integer NOT NULL,
    "subject" varchar(255) NOT NULL
);
ALTER TABLE "topic" ADD CONSTRAINT forum_id FOREIGN KEY ("forum_id") REFERENCES "forum" ("id");

-- Initials
insert into "topic" ("forum_id", "subject") values (2, 'D''artagnian');

select count(*) from cicero_forum;

-- this line lacks ; at the end to allow people to be sloppy and omit it in one-liners
COMMIT

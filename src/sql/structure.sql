CREATE TYPE source AS (name character varying, content text, language character varying);

CREATE TABLE snippet (
    id serial PRIMARY KEY,
    author_id integer NOT NULL,
    keywords character varying[],
    title character varying NOT NULL,
    codes source[] NOT NULL,
    created timestamp without time zone DEFAULT now() NOT NULL,
    updated timestamp without time zone DEFAULT now() NOT NULL
);

CREATE TABLE author (
    id serial PRIMARY KEY,
    name character varying NOT NULL UNIQUE,
    password character varying NOT NULL
);

ALTER TABLE ONLY snippet
    ADD CONSTRAINT snippet_author_id_fkey FOREIGN KEY (author_id) REFERENCES author(id);

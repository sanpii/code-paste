CREATE TYPE source AS (name character varying, content text);

CREATE TABLE snippet (
    id serial PRIMARY KEY,
    keywords character varying[],
    language character varying NOT NULL,
    title character varying NOT NULL,
    code source NOT NULL,
    created timestamp without time zone DEFAULT now() NOT NULL,
    updated timestamp without time zone DEFAULT now() NOT NULL
);

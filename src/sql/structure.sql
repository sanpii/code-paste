CREATE TYPE source AS (name character varying, content text, language character varying);

CREATE TABLE snippet (
    id serial PRIMARY KEY,
    keywords character varying[],
    title character varying NOT NULL,
    codes source[] NOT NULL,
    created timestamp without time zone DEFAULT now() NOT NULL,
    updated timestamp without time zone DEFAULT now() NOT NULL
);

CREATE TABLE contributions
(
    id bigint NOT NULL,
    title text COLLATE pg_catalog."default" NOT NULL,
    "projectName" text COLLATE pg_catalog."default" NOT NULL,
    url text COLLATE pg_catalog."default" NOT NULL,
    state text COLLATE pg_catalog."default" NOT NULL,
    "createdAt" timestamp without time zone NOT NULL,
    "updatedAt" timestamp without time zone,
    "closedAt" timestamp without time zone,
    CONSTRAINT contributions_pkey PRIMARY KEY (id)
)

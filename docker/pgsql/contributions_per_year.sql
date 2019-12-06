-- Adminer 4.7.5 PostgreSQL dump

-- DROP TABLE IF EXISTS "contributions_per_year";

CREATE TABLE "public"."contributions_per_year" (
    "year" smallint NOT NULL,
    "merged" integer NOT NULL,
    "ids" bigint[],
    "total" integer GENERATED ALWAYS AS (array_length(ids, 1)) STORED,
    "merged_part" numeric GENERATED ALWAYS AS (merged::numeric / array_length(ids, 1)::numeric) STORED,
    CONSTRAINT "contributions_per_year_year" PRIMARY KEY ("year")
) WITH (oids = false);


-- 2019-12-06 08:11:20.499653+00

CREATE TABLE projections (
  no BIGSERIAL,
  name VARCHAR(150) NOT NULL,
  position integer,
  PRIMARY KEY (no),
  UNIQUE (name)
);
CREATE INDEX on projections (name);

-- Mcollective actions
DROP TABLE IF EXISTS mcollective_logs CASCADE;
CREATE TABLE mcollective_logs (
  id          SERIAL PRIMARY KEY,
  actionid    VARCHAR(33)  NOT NULL,
  login       VARCHAR(50)  NOT NULL,
  fullname    VARCHAR(256) NOT NULL,
  agent       VARCHAR(50)  NOT NULL,
  filter      VARCHAR(256) NOT NULL,
  pf          VARCHAR(256) NOT NULL,
  received_at TIMESTAMP    NOT NULL
);
CREATE INDEX mcollective_logs_actionid ON mcollective_logs (actionid);
CREATE INDEX mcollective_logs_username ON mcollective_logs (login);
CREATE INDEX mcollective_logs_pf ON mcollective_logs (pf);

-- List of mco discovered nodes for actions
DROP TABLE IF EXISTS mcollective_logs_discovered CASCADE;
CREATE TABLE mcollective_logs_discovered (
  id       SERIAL PRIMARY KEY,
  log_id   INTEGER REFERENCES mcollective_logs (id),
  hostname VARCHAR(256) NOT NULL
);
CREATE INDEX mcollective_logs_discovered_logid ON mcollective_logs_discovered (id);
CREATE INDEX mcollective_logs_discovered_hostname ON mcollective_logs_discovered (hostname);


-- Result logs
DROP TABLE IF EXISTS mcollective_actions_logs CASCADE;
CREATE TABLE mcollective_actions_logs (
  id          SERIAL PRIMARY KEY,
  actionid    VARCHAR(33)  NOT NULL,
  requestid   VARCHAR(50)  NOT NULL,
  type        VARCHAR(30),
  caller      VARCHAR(50),
  hostname    VARCHAR(50),
  agent       VARCHAR(30),
  action      VARCHAR(30),
  sender      VARCHAR(256) NOT NULL,
  statuscode  INTEGER,
  result      TEXT,
  received_at TIMESTAMP    NOT NULL
);
CREATE INDEX mcollective_actions_logs_actionid ON mcollective_actions_logs (actionid);
CREATE INDEX mcollective_actions_logs_actionid_requestid ON mcollective_actions_logs (actionid, requestid);
CREATE INDEX mcollective_actions_logs_sender ON mcollective_actions_logs (sender);

-- Agent table metadatas
DROP TABLE IF EXISTS mcollective_agents_metadata CASCADE;
CREATE TABLE mcollective_agents_metadata (
  id   	         SERIAL PRIMARY KEY,
  name 	   	 VARCHAR(256) NOT NULL,
  description 	 VARCHAR(256)
);
CREATE INDEX mcollective_agents_metadata_name ON mcollective_agents_metadata (name);

-- Actions tables metadatas
DROP TABLE IF EXISTS mcollective_actions_metadata CASCADE;
CREATE TABLE mcollective_actions_metadata (
  id   	         SERIAL PRIMARY KEY,
  agent_id	 INTEGER references mcollective_agents_metadata (id),
  name 	   	 VARCHAR(256) NOT NULL,
  description 	 VARCHAR(256),
  long_detail	 VARCHAR(256),
  short_detail	 VARCHAR(256),
  ihm_icon	 VARCHAR(32),
  limit_number	 INTEGER,
  limit_host	 VARCHAR(256)
);
CREATE INDEX mcollective_actions_metadata_name ON mcollective_actions_metadata (name);

-- Arguments metadata tables
DROP TABLE IF EXISTS mcollective_actions_arguments_metadata CASCADE;
CREATE TABLE mcollective_actions_arguments_metadata (
  id   	         SERIAL PRIMARY KEY,
  action_id	 INTEGER references mcollective_actions_metadata (id),
  name 	   	 VARCHAR(256) NOT NULL,
  description 	 VARCHAR(256),
  mandatory	 SMALLINT,
  type		 VARCHAR(16),
  value		 VARCHAR(128)
);
CREATE INDEX mcollective_actions_arguments_metadata_name ON mcollective_actions_arguments_metadata (name);

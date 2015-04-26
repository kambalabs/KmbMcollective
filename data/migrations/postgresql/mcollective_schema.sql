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
  parameters  TEXT,
  received_at TIMESTAMP    NOT NULL
);
CREATE INDEX mcollective_logs_actionid ON mcollective_logs (actionid);
CREATE INDEX mcollective_logs_username ON mcollective_logs (login);
CREATE INDEX mcollective_logs_pf ON mcollective_logs (pf);

-- New mcollective actions
DROP TABLE IF EXISTS action_logs CASCADE;
CREATE TABLE action_logs (
  actionid               VARCHAR(33) NOT NULL,
  environment            VARCHAR(256),
  parameters             TEXT,
  ihm_icon               VARCHAR(256),
  description            TEXT,
  source                 VARCHAR(256),
  login                  VARCHAR(50),
  fullname               VARCHAR(255),
  created_at             TIMESTAMP NOT NULL,
  finished               BOOLEAN NOT NULL DEFAULT false
);
CREATE INDEX action_logs_actionid ON action_logs (actionid);
CREATE INDEX action_logs_login ON action_logs (login);
CREATE INDEX action_logs_env ON action_logs (environment);

-- New mcollective command tables
DROP TABLE IF EXISTS command_logs CASCADE;
CREATE TABLE command_logs (
       requestid          VARCHAR(50) NOT NULL PRIMARY KEY,
       actionid           VARCHAR(33) REFERENCES action_logs (actionid) ON DELETE CASCADE
);
CREATE INDEX command_logs_requestid ON command_logs (requestid);
CREATE INDEX command_logs_actionid ON command_logs (actionid);

-- New mcollective reply tables
DROP TABLE IF EXISTS command_reply_logs;
CREATE TABLE command_reply_logs (
  id                     SERIAL PRIMARY KEY,
  hostname               VARCHAR(256),
  username                   VARCHAR(256),
  statuscode             INTEGER,
  result                 TEXT,
  requestid              VARCHAR(50) REFERENCES command_logs (requestid) ON DELETE CASCADE,
  agent                  VARCHAR(50),
  action                 VARCHAR(50),
  finished               BOOLEAN NOT NULL DEFAULT false
);
CREATE INDEX command_reply_logs_id ON command_reply_logs (id);
CREATE INDEX command_reply_logs_user ON command_reply_logs (user);
CREATE INDEX command_reply_logs_hostname ON command_reply_logs (hostname);


-- List of mco discovered nodes for actions
DROP TABLE IF EXISTS mcollective_logs_discovered CASCADE;
CREATE TABLE mcollective_logs_discovered (
  id       SERIAL PRIMARY KEY,
  log_id   INTEGER REFERENCES mcollective_logs (id) ON DELETE CASCADE,
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
  finished    BOOLEAN,
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
  agent_id	 INTEGER references mcollective_agents_metadata (id) ON DELETE CASCADE,
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
  action_id	 INTEGER references mcollective_actions_metadata (id) ON DELETE CASCADE,
  name 	   	 VARCHAR(256) NOT NULL,
  description 	 VARCHAR(256),
  mandatory	 BOOLEAN,
  type		 VARCHAR(16),
  value		 VARCHAR(128)
);
CREATE INDEX mcollective_actions_arguments_metadata_name ON mcollective_actions_arguments_metadata (name);

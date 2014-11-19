DROP TABLE IF EXISTS mcollective_logs CASCADE;
CREATE TABLE mcollective_logs (
  id             SERIAL PRIMARY KEY,
  actionid       VARCHAR(33) NOT NULL,
  username	 VARCHAR(50) NOT NULL,
  fullname	 VARCHAR(256) NOT NULL,
  agent		 VARCHAR(50) NOT NULL,
  filter	 VARCHAR(256) NOT NULL,
  pf		 VARCHAR(256) NOT NULL
);
CREATE INDEX mcollective_logs_actionid ON mcollective_logs (actionid);
CREATE INDEX mcollective_logs_username ON mcollective_logs (username);
CREATE INDEX mcollective_logs_pf ON mcollective_logs (pf);

DROP TABLE IF EXISTS mcollective_logs_discovered CASCADE;
CREATE TABLE mcollective_logs_discovered (
  id             SERIAL PRIMARY KEY,
  log_id	 INTEGER references mcollective_logs(id),
  hostname	 VARCHAR(256) NOT NULL
);
CREATE INDEX mcollective_logs_discovered_logid ON mcollective_logs_discovered(id);
CREATE INDEX mcollective_logs_discovered_hostname ON mcollective_logs_discovered(hostname);


DROP TABLE IF EXISTS mcollective_actions_logs CASCADE;
CREATE TABLE mcollective_actions_logs (
  id             SERIAL PRIMARY KEY,
  actionid       VARCHAR(33) NOT NULL,
  requestid	 VARCHAR(50) NOT NULL,
  caller	 varchar(50),
  hostname	 VARCHAR(50),
  agent		 VARCHAR(30),
  action	 VARCHAR(30),
  sender	 VARCHAR(256) NOT NULL,
  statuscode	 INTEGER,
  result	 TEXT,
  received_at	 TIMESTAMP NOT NULL
);
CREATE INDEX mcollective_actions_logs_actionid ON mcollective_actions_logs (actionid);
CREATE INDEX mcollective_actions_logs_actionid_requestid ON mcollective_actions_logs (actionid,requestid);
CREATE INDEX mcollective_actions_logs_sender ON mcollective_actions_logs (sender);

DROP TABLE IF EXISTS `mcollective_logs`;
CREATE TABLE `mcollective_logs` (
`id` INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
`actionid` VARCHAR(32) NOT NULL DEFAULT '',
`username` VARCHAR(256) NOT NULL DEFAULT '',
`fullname` VARCHAR(256) NOT NULL DEFAULT '',
`agent` VARCHAR(256) NOT NULL DEFAULT '',
`filter` VARCHAR(256) NOT NULL DEFAULT '',
`pf` VARCHAR(256) NOT NULL DEFAULT '',
);
CREATE INDEX `mcollective_logs_actionid` ON `mcollective_logs` (`actionid`);
CREATE INDEX `mcollective_logs_username` ON `mcollective_logs` (`username`);
CREATE INDEX `mcollective_logs_pf` ON `mcollective_logs` (`pf`);

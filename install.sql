DROP TABLE	wcf1_woltlab_pluginstore_file;
CREATE TABLE	wcf1_woltlab_pluginstore_file (
	fileID				INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	name				VARCHAR(255) NOT NULL,
	lastNameUpdateTime		INT(10) DEFAULT NULL
);
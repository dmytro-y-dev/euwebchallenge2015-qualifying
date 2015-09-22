CREATE DATABASE `test_jobs_scheduler` CHARACTER SET = utf8;
USE `test_jobs_scheduler`;

CREATE TABLE images (job_id INT NOT NULL, remoteAddress VARCHAR(255) NOT NULL, localAddress VARCHAR(255) DEFAULT NULL, contentType VARCHAR(100) DEFAULT NULL, width INT DEFAULT NULL, height INT DEFAULT NULL, size INT DEFAULT NULL, status VARCHAR(30) NOT NULL, INDEX IDX_E01FBE6ABE04EA9 (job_id), PRIMARY KEY(job_id, remoteAddress)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE jobs (id INT AUTO_INCREMENT NOT NULL, htmlpage VARCHAR(255) NOT NULL, dateFinished DATETIME DEFAULT NULL, dateStarted DATETIME DEFAULT NULL, status VARCHAR(30) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
ALTER TABLE images ADD CONSTRAINT FK_E01FBE6ABE04EA9 FOREIGN KEY (job_id) REFERENCES jobs (id);

INSERT INTO jobs VALUES
(1, 'testpage1.com', NULL, NULL, "pending"),
(2, 'testpage2.com', NULL, NULL, "pending");

INSERT INTO images VALUES
(1, 'testpage1.com/kitten1.jpg', NULL, NULL, NULL, NULL, NULL, "in process"),
(1, 'testpage1.com/kitten2.jpg', NULL, NULL, NULL, NULL, NULL, "in process"),
(1, 'testpage1.com/kitten3.jpg', NULL, NULL, NULL, NULL, NULL, "in process"),
(2, 'testpage2.com/kitten.png', NULL, NULL, NULL, NULL, NULL, "in process");

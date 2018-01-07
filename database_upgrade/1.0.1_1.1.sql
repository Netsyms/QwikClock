/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
CREATE TABLE IF NOT EXISTS `jobs` (
  `jobid` INT(11) NOT NULL AUTO_INCREMENT,
  `jobname` VARCHAR(200) NOT NULL,
  `jobcode` VARCHAR(200) NULL DEFAULT NULL,
  `color` VARCHAR(45) NULL DEFAULT NULL,
  `deleted` TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`jobid`),
  UNIQUE INDEX `jobid_UNIQUE` (`jobid` ASC))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `job_groups` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `groupid` INT(11) NOT NULL,
  `jobid` INT(11) NOT NULL,
  PRIMARY KEY (`id`, `groupid`, `jobid`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC),
  INDEX `fk_job_groups_jobs1_idx` (`jobid` ASC),
  CONSTRAINT `fk_job_groups_jobs1`
    FOREIGN KEY (`jobid`)
    REFERENCES `qwikclock`.`jobs` (`jobid`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `job_tracking` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `uid` INT(11) NOT NULL,
  `jobid` INT(11) NOT NULL,
  `start` DATETIME NULL DEFAULT NULL,
  `end` DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`id`, `uid`, `jobid`),
  INDEX `fk_job_tracking_jobs1_idx` (`jobid` ASC),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC),
  CONSTRAINT `fk_job_tracking_jobs1`
    FOREIGN KEY (`jobid`)
    REFERENCES `qwikclock`.`jobs` (`jobid`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

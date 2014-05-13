/**
 * Operator Level Table
 *
 * @author rutoru
 * @package Runa-CCA
 */
CREATE TABLE operator_level
(
`operator_level_id` int PRIMARY KEY NOT NULL UNIQUE,
`operator_level_name` varchar(32) NOT NULL
)
ENGINE InnoDB;

/**
 * Operator Table
 *
 * @author rutoru
 * @package Runa-CCA
 */
CREATE TABLE operator
(
`operator_id` varchar(20) PRIMARY KEY NOT NULL UNIQUE,
`password` varchar(255),
`last_name` varchar(32),
`first_name` varchar(32),
`client_name` varchar(32),
`telnum` varchar(20),
`operator_level_id` int,
INDEX (operator_level_id),
FOREIGN key(`operator_level_id`) REFERENCES operator_level(`operator_level_id`)
)
ENGINE InnoDB;

/**
 * Queue Table
 *
 * @author rutoru
 * @package Runa-CCA
 */
CREATE TABLE queue
(
`queue_id` varchar(20) PRIMARY KEY NOT NULL UNIQUE,
`queue_name` varchar(32) NOT NULL,
`max_size` int,
`action_url` varchar(128),
`wait_url` varchar(128),
`guidance_url` varchar(128),
`twilio_queue_id` varchar(128),
INDEX (twilio_queue_id)
)
ENGINE InnoDB;

/**
 * OperatorQueue Table
 * A pivot table for operator table and queue table
 *
 * @author rutoru
 * @package Runa-CCA
 */
CREATE TABLE operator_queue
(
`id` INT PRIMARY KEY NOT NULL UNIQUE AUTO_INCREMENT,
`operator_id` varchar(20) NOT NULL,
`queue_id` varchar(32) NOT NULL,
FOREIGN KEY(`operator_id`) REFERENCES operator(`operator_id`),
FOREIGN key(`queue_id`) REFERENCES queue(`queue_id`)
)
ENGINE InnoDB;

/**
 * EnqueueData Table
 *
 * @author rutoru
 * @package Runa-CCA
 */
CREATE TABLE enqueue_data
(
`CallSid` CHAR(34) PRIMARY KEY NOT NULL UNIQUE,
`From` VARCHAR(255),
`To` VARCHAR(255),
`CallStatus` VARCHAR(15),
`ApiVersion` CHAR(10),
`Direction` VARCHAR(15),
`ForwardedFrom` VARCHAR(255),
`CallerName` VARCHAR(255),
`QueueResult` VARCHAR(15),
`QueueSid` CHAR(34),
`QueueTime` INT,
`updated_at` DATETIME,
`created_at` DATETIME,
 INDEX cst_idx(CallStatus),
 INDEX rqu_idx(QueueResult),
 INDEX qu_idx(QueueSid)
)
ENGINE InnoDB;

/**
 * QueueData Table
 *
 * @author rutoru
 * @package Runa-CCA
 */
CREATE TABLE queue_data
(
`CallSid` CHAR(34) PRIMARY KEY NOT NULL UNIQUE,
`From` VARCHAR(255),
`To` VARCHAR(255),
`CallStatus` VARCHAR(15),
`ApiVersion` CHAR(10),
`Direction` VARCHAR(15),
`ForwardedFrom` VARCHAR(255),
`CallerName` VARCHAR(255),
`QueueSid` CHAR(34),
`QueueTime` INT,
`DequeingCallSid` CHAR(34),
`updated_at` DATETIME,
`created_at` DATETIME,
 INDEX cst_idx(CallStatus),
 INDEX qu_idx(QueueSid),
 INDEX dqu_idx(DequeingCallSid)
)
ENGINE InnoDB;

/**
 * StatusCallback Table
 *
 * @author rutoru
 * @package Runa-CCA
 */
CREATE TABLE statuscallback_data
(
`CallSid` CHAR(34) PRIMARY KEY NOT NULL UNIQUE,
`From` VARCHAR(255),
`To` VARCHAR(255),
`CallStatus` VARCHAR(15),
`ApiVersion` CHAR(10),
`Direction` VARCHAR(15),
`ForwardedFrom` VARCHAR(255),
`CallerName` VARCHAR(255),
`CallDuration` INT,
`RecordingUrl` VARCHAR(255),
`RecordingSid` CHAR(34),
`RecordingDuration` INT,
`updated_at` DATETIME,
`created_at` DATETIME,
 INDEX cst_idx(CallStatus),
 INDEX rsid_idx(RecordingSid)
)
ENGINE InnoDB;

/**
 * Sample Data
 * Operator Id is 'admin', Password is 'test'.
 *
 * @author rutoru
 * @package Runa-CCA
 */
INSERT INTO `twilio`.`operator_level` (`operator_level_id`, `operator_level_name`) VALUES (1, 'SystemAdmin'),(2, 'Supervisor'), (3, 'Operator');
INSERT INTO `twilio`.`operator` (`operator_id`, `password`, `last_name`, `first_name`, `client_name`, `telnum`, `operator_level_id`) VALUES('admin', '$2y$10$XWdgTixMOjWgn5SRXekSv.j8oBXkgZIlNfLxLpXBJUccar3jyQ8ay', 'admin', 'admin', 'admin', '+815099998888', 1);
INSERT INTO `queue` (`queue_id`, `queue_name`, `action_url`, `wait_url`, `guidance_url`, `twilio_queue_id`) VALUES ('sample', 'sample', 'http://localhost/runa-cca/twilio/callflow/newservice/enqueaction', '/runa-cca/twilio/callflow/newservice/wait', '/runa-cca/twilio/callflow/newservice/guidance', NULL);
INSERT INTO `twilio`.`operator_queue` (`id`, `operator_id`, `queue_id`) VALUES ('1', 'admin', 'sample');


-- VERSION 0.9
-- DATE: 	8.6.08
-- Changes:	This update changed the table camera to hold only a camera_id and description.  The original
-- 		camera table is renamed to video and given a flagged value.

CREATE TABLE camera(
	camera_id	INT UNSIGNED	NOT NULL,
	hostname	VARCHAR(255)	NOT NULL,
	description	VARCHAR(255),
	PRIMARY KEY(camera_id)
)ENGINE=INNODB
;

CREATE TABLE video(
    vid_id       INT UNSIGNED   AUTO_INCREMENT,
    time         INT UNSIGNED   NOT NULL,
    video_name   VARCHAR(255)   NOT NULL,
    picture_name VARCHAR(255)   NOT NULL,
    event        INT UNSIGNED   NOT NULL,
    camera_id    INT UNSIGNED   NOT NULL,
    flagged      INT UNSIGNED   NOT NULL   DEFAULT 0,
    PRIMARY KEY(vid_id)
)ENGINE=INNODB
;

CREATE INDEX RefCam ON camera(camera_id)
;

ALTER TABLE video ADD CONSTRAINT RefCam
    FOREIGN KEY (camera_id)
    REFERENCES camera(camera_id)
;

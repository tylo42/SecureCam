--- Copyright 2008, 2009, 2010, 2011 Tyler Hyndman
--- 
--- This file is part of SecureCam.
--- 
--- SecureCam is free software: you can redistribute it and/or modify
--- it under the terms of the GNU General Public License as published by
--- the Free Software Foundation, either version 3 of the License, or
--- (at your option) any later version.
---  
--- SecureCam is distributed in the hope that it will be useful,
--- but WITHOUT ANY WARRANTY; without even the implied warranty of
--- MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
--- GNU General Public License for more details.
--- 
--- You should have received a copy of the GNU General Public License
--- along with SecureCam.  If not, see <http://www.gnu.org/licenses/>.

CREATE TABLE camera(
	camera_id	INT UNSIGNED	NOT NULL,
	hostname	VARCHAR(255)	NOT NULL,
   port INT UNSIGNED NOT NULL,
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

USE phpscheduleIt;

# create announcements table
CREATE TABLE announcements (
  announcementid CHAR(16) NOT NULL PRIMARY KEY,
  announcement CHAR(255) NOT NULL DEFAULT '',
  number SMALLINT(3) NOT NULL DEFAULT 0,
  start_datetime INT,
  end_datetime INT
  );

# Create indexes on announcements table #
CREATE INDEX announcements_startdatetime ON announcements(start_datetime);
CREATE INDEX announcements_enddatetime ON announcements(end_datetime);

# add support for pending reservations
ALTER TABLE reservations ADD COLUMN is_pending SMALLINT(1) NOT NULL DEFAULT 0 AFTER is_blackout;
CREATE INDEX reservations_ispending ON reservations (is_pending);
ALTER TABLE resources ADD COLUMN approval SMALLINT(1);
ALTER TABLE login ADD COLUMN e_app CHAR(1) NOT NULL DEFAULT 'y' AFTER e_del;

# add support for logon name
ALTER TABLE login ADD COLUMN logon_name CHAR(30);
CREATE INDEX login_logonname ON login (logon_name);

#add support for multi-day reservations
ALTER TABLE reservations ADD COLUMN start_date int NOT NULL DEFAULT 0 AFTER date;
ALTER TABLE reservations ADD COLUMN end_date int NOT NULL DEFAULT 0 AFTER start_date;
CREATE INDEX reservations_startdate ON reservations (start_date);
CREATE INDEX reservations_enddate ON reservations (end_date);

UPDATE reservations SET start_date = date;
UPDATE reservations SET end_date = date;

ALTER TABLE reservations DROP COLUMN date;
ALTER TABLE resources ADD COLUMN allow_multi SMALLINT(1);

# add support for multiple users per reservation
create table reservation_users (
  resid char(16) not null,
  memberid char(16) not null,
  owner smallint(1),
  invited smallint(1),
  perm_modify smallint(1),
  perm_delete smallint(1),
  accept_code char(16),
  primary key(resid, memberid)
  );

create index resusers_resid on reservation_users (resid);
create index resusers_memberid on reservation_users (memberid);
create index resusers_owner on reservation_users (owner);

# add support for multi-admins
alter table login add column is_admin smallint(1) default 0;

insert into reservation_users select resid, memberid, 1, 0, 0, 0, null from reservations;
alter table reservations drop column memberid, drop index res_memberid;
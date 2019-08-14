# phpScheduleIt 1.1.0 #
drop database if exists phpScheduleIt;
create database phpScheduleIt;
use phpScheduleIt;

# Create announcements table #
create table announcements (
    announcementid varchar(16) not null primary key,
    announcement varchar(255) not null default '',
    number smallint(3) not null default '0',
    start_datetime INT,
    end_datetime INT
);

# Create indexes on announcements table #
create index announcements_startdatetime on announcements(start_datetime);
create index announcements_enddatetime on announcements(end_datetime);

# Create login table #
create table login (
  memberid char(16) not null primary key,
  email char(75) not null,
  password char(32) not null,
  fname char(30) not null,
  lname char(30) not null,
  phone char(16) not null,
  institution char(255),
  position char(100),
  e_add char(1) not null default 'y',
  e_mod char(1) not null default 'y',
  e_del char(1) not null default 'y',
  e_app char(1) not null default 'y',
  e_html char(1) not null default 'y',
  logon_name char(30),
  is_admin smallint(1) default 0
  );

# Create indexes on login table #
create index login_memberid on login (memberid);
create index login_email on login (email);
create index login_password on login (password);
create index login_logonname on login (logon_name);


# Create reservations table #  
create table reservations (
  resid char(16) not null primary key,
  machid char(16) not null,
  scheduleid char(16) not null,
  start_date int not null default 0,
  end_date int not null default 0,
  startTime integer not null,
  endTime integer not null,
  created integer not null,
  modified integer,
  parentid char(16),
  is_blackout smallint(1) not null default 0,
  is_pending smallint(1) not null default 0,
  summary text
  );

# Create indexes on reservations table #
create index res_resid on reservations (resid);
create index res_machid on reservations (machid);
create index res_scheduleid on reservations (scheduleid);
create index reservations_startdate on reservations (start_date);
create index reservations_enddate on reservations (end_date);
create index res_startTime on reservations (startTime);
create index res_endTime on reservations (endTime);
create index res_created on reservations (created);
create index res_modified on reservations (modified);
create index res_parentid on reservations (parentid);
create index res_isblackout on reservations (is_blackout);
create index reservations_pending on reservations (is_pending);

# Create resources table #
create table resources (
  machid char(16) not null primary key,
  scheduleid char(16) not null,
  name char(75) not null,
  location char(250),
  rphone char(16),
  notes text,
  status char(1) not null default 'a',
  minRes integer not null,
  maxRes integer not null,
  autoAssign smallint(1),
  approval smallint(1),
  allow_multi smallint(1)
  );

# Create indexes on resources table #
create index rs_machid on resources (machid);
create index rs_scheduleid on resources (scheduleid);
create index rs_name on resources (name);
create index rs_status on resources (status);
  
# Create permission table #
create table permission (
  memberid char(16) not null,
  machid char(16) not null,
  primary key(memberid, machid)
  );
  
# Create indexes on permission table #
create index per_memberid on permission (memberid);
create index per_machid on permission (machid);

# Create schedules table #
create table schedules (
  scheduleid char(16) not null primary key,
  scheduleTitle char(75),
  dayStart integer not null,
  dayEnd integer not null,
  timeSpan integer not null,
  timeFormat integer not null,
  weekDayStart integer not null,
  viewDays integer not null,
  usePermissions smallint(1),
  isHidden smallint(1),
  showSummary smallint(1),
  adminEmail char(75),
  isDefault smallint(1),
  dayOffset integer
  );
  
# Create default schedule #
insert into schedules values ('sc1423642970aa9f','default',480,1200,30,12,0,7,0,0,1,'admin@email.com',1,0);

# Create indexes on schedules table #
create index sh_scheduleid on schedules (scheduleid);
create index sh_hidden on schedules (isHidden);
create index sh_perms on schedules (usePermissions);

# Create schedule permission tables
create table schedule_permission (
  scheduleid char(16) not null,
  memberid char(16) not null,
  primary key(scheduleid, memberid)
  );

# Create schedule permission indexes #
create index sp_scheduleid on schedule_permission (scheduleid);
create index sp_memberid on schedule_permission (memberid);
  
# Create reservation/user association table #
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

grant select, insert, update, delete
on phpScheduleIt.*
to schedule_user@localhost identified by 'password';

#SET PASSWORD FOR schedule_user@localhost = OLD_PASSWORD('password');